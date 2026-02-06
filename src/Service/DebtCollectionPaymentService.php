<?php

namespace App\Service;

use App\Entity\DebtCollectionPayment;
use App\Enum\DebtCollectionPaymentStatus;
use App\Enum\DebtCollectionStatus;
use App\Model\Integration\Common\PaymentProcessor\GenericPaymentProcessorTransactionStatus;
use App\Repository\DebtCollectionPaymentRepository;
use App\Repository\DebtCollectionRepository;
use App\Repository\PaymentTransactionalAccountRepository;
use App\Service\PaymentProcessor\PaymentProcessorProvider;
use App\Util\MoneyUtil;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

readonly class DebtCollectionPaymentService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
        private SerializerInterface $serializer,
        private PaymentProcessorProvider $paymentProcessorProvider,
        private PaymentTransactionalAccountRepository $paymentTransactionalAccountRepository,
        private DebtCollectionPaymentRepository $debtCollectionPaymentRepository,
        private DebtCollectionRepository $debtCollectionRepository
    )
    {
    }

    public function processDebtPayment(string $transactionReference): void
    {
        $this->logger->info("Processing debt payment for reference: $transactionReference");

        // 1. Find the transactional account
        $transactionAccount = $this->paymentTransactionalAccountRepository->findOneBy([
            'transactionReference' => $transactionReference,
        ]);
        if (!$transactionAccount) {
            $this->logger->error("PaymentTransactionalAccount not found for reference: $transactionReference");
            return;
        }

        // 2. Find the pending payment record
        $debtPayment = $this->debtCollectionPaymentRepository->findOneBy([
            'paymentReference' => $transactionReference,
        ]);
        if (!$debtPayment) {
            $this->logger->error("DebtCollectionPayment not found for reference: $transactionReference");
            return;
        }

        // 3. Check if already processed
        if ($debtPayment->getStatus() === DebtCollectionPaymentStatus::APPROVED) {
            $this->logger->info("Payment already processed for reference: $transactionReference");
            return;
        }

        // 4. Verify payment with gateway
        $paymentProcessor = $this->paymentProcessorProvider->getProcessor($transactionAccount->getProvider());
        if (!$paymentProcessor) {
            $this->logger->error("Payment processor not available for provider: {$transactionAccount->getProvider()->value}");
            return;
        }

        $verifyResponse = $paymentProcessor->verifyTransaction($transactionReference);
        if (!$verifyResponse) {
            $this->logger->error("Failed to verify transaction: $transactionReference");
            return;
        }

        $gatewayStatus = $verifyResponse->getTransactionStatus();
        $gatewayResponseObject = $this->serializer->normalize($verifyResponse, null, [
            AbstractObjectNormalizer::PRESERVE_EMPTY_OBJECTS => true,
        ]);

        $this->logger->info("Payment verification result: {$gatewayStatus->value} for reference: $transactionReference");

        // 5. Update payment record with gateway response
        $debtPayment->setGatewayResponseObject($gatewayResponseObject);

        if ($gatewayStatus !== GenericPaymentProcessorTransactionStatus::SUCCESSFUL) {
            $debtPayment->setStatus(DebtCollectionPaymentStatus::REJECTED->value);
            $this->entityManager->persist($debtPayment);
            $this->entityManager->flush();
            $this->logger->info("Payment not successful, marked as REJECTED");
            return;
        }

        // 6. Process successful payment
        $debtCollection = $debtPayment->getDebtCollection();
        $creditor = $debtCollection->getCreditor();
        $creditorWallet = $creditor->getWallet();

        // Credit creditor's wallet with the payment amount (already in Kobo)
        $paymentAmountKobo = (int) $debtPayment->getAmount();
        
        // LOCKING: Prevent concurrent balance updates
        $this->entityManager->lock($creditorWallet, LockMode::PESSIMISTIC_WRITE);
        $this->entityManager->refresh($creditorWallet);

        $newBalance = MoneyUtil::add($creditorWallet->getBalance(), $paymentAmountKobo);
        $creditorWallet->setBalance((string) $newBalance);
        $this->entityManager->persist($creditorWallet);

        // Update debt collection balance
        $currentUnpaid = (int) $debtCollection->getAmountUnpaid();
        $newUnpaid = max(0, MoneyUtil::subtract($currentUnpaid, $paymentAmountKobo));
        $debtCollection->setAmountUnpaid((string) $newUnpaid);

        // Update debt status
        if ($newUnpaid === 0) {
            $debtCollection->setStatus(DebtCollectionStatus::PAID->value);
        } else {
            $debtCollection->setStatus(DebtCollectionStatus::PARTIAL->value);
        }
        $this->entityManager->persist($debtCollection);

        // Mark payment as approved
        $debtPayment->setStatus(DebtCollectionPaymentStatus::APPROVED->value);
        $debtPayment->setLastModifiedAt(new \DateTimeImmutable());
        $this->entityManager->persist($debtPayment);

        $this->entityManager->flush();

        $this->logger->info("Debt payment processed successfully. Creditor wallet credited with: " . MoneyUtil::toNaira($paymentAmountKobo));
    }
}
