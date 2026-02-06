<?php

namespace App\Service;

use App\Entity\WalletFunding;
use App\Enum\WalletFundingStatus;
use App\Model\Integration\Common\PaymentProcessor\GenericPaymentProcessorTransactionStatus;
use App\Model\Integration\Paystack\PaystackTransactionStatus;
use App\Repository\PaymentTransactionalAccountRepository;
use App\Service\PaymentProcessor\PaymentProcessorProvider;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

readonly class WalletFundingService
{
    public function __construct(
        private EntityManagerInterface   $entityManager,
        private LoggerInterface          $logger,
        private SerializerInterface      $serializer,
        private PaymentProcessorProvider $paymentProcessorProvider,
        private PaymentTransactionalAccountRepository $paymentTransactionalAccountRepository
    )
    {
    }

    public function processProcessingWalletFunding(WalletFunding $walletFunding): void
    {
        if ($walletFunding->getStatus() !== WalletFundingStatus::PROCESSING) {
            $this->logger->error("Could not process the Wallet Funding with ID: {walletFundingId} as it is in an invalid state: {status}", [
                'walletFundingId' => $walletFunding->getId(),
                'status' => $walletFunding->getStatus(),
            ]);
            return;
        }

        $transactionReference = $walletFunding->getReference();
        $transactionAccount = $this->paymentTransactionalAccountRepository->findOneBy([
            'transactionReference' => $transactionReference,
        ]);
        if (!$transactionAccount) {
            $this->logger->error("Could not process the Wallet Funding with ID: {walletFundingId} as we could not fetch transaction account with reference: {transactionReference}", [
                'walletFundingId' => $walletFunding->getId(),
                'transactionReference' => $transactionReference,
            ]);
            return;
        }

        $paymentProcessor = $this->paymentProcessorProvider->getProcessor($transactionAccount->getProvider());
        if (!$paymentProcessor) {
            $this->logger->error("Could not process the Wallet Funding with ID: {walletFundingId} as we couldn't update processor for provider: {provider}", [
                'walletFundingId' => $walletFunding->getId(),
                'provider' => $transactionAccount->getProvider(),
            ]);
            return;
        }

        $verifyTransactionResponse = $paymentProcessor->verifyTransaction($transactionReference);
        if (!$verifyTransactionResponse) {
            $this->logger->error("Failed to obtain status of wallet funding with code: {transactionReference}", [
                'transactionReference' => $transactionReference,
            ]);
            return;
        }

        $gatewayStatus = $verifyTransactionResponse->getTransactionStatus();
        $gatewayResponseObject = $this->serializer->normalize($verifyTransactionResponse, null, [
            AbstractObjectNormalizer::PRESERVE_EMPTY_OBJECTS => true,
        ]);

        $this->logger->info("Wallet Funding Status Retrieved!\nReference: {transactionReference}.\nStatus: {status}.", [
            'transactionReference' => $transactionReference,
            'status' => $gatewayStatus,
        ]);

        $walletFundingStatus = match ($gatewayStatus) {
            GenericPaymentProcessorTransactionStatus::SUCCESSFUL => WalletFundingStatus::SUCCESS,
            GenericPaymentProcessorTransactionStatus::FAILED => WalletFundingStatus::FAILED,
            default => WalletFundingStatus::PROCESSING,
        };

        $walletFundingMessage = match ($gatewayStatus) {
            GenericPaymentProcessorTransactionStatus::SUCCESSFUL => 'Transaction Successful',
            GenericPaymentProcessorTransactionStatus::FAILED => 'Transaction Not Successful',
            default => "Transaction is being processed",
        };

        $this->logger->info("Wallet Funding with ID: {walletFundingId} has status: {status} from gateway!", [
            'walletFundingId' => $walletFunding->getId(),
            'status' => $gatewayStatus,
        ]);

        $walletFunding
            ->setStatus($walletFundingStatus)
            ->setStatusMessage($walletFundingMessage ?? $walletFunding->getStatusMessage())
            ->setGatewayResponseObject($gatewayResponseObject);
        $this->entityManager->persist($walletFunding);

        if ($walletFundingStatus === WalletFundingStatus::SUCCESS) {
            $wallet = $walletFunding->getWallet();
            // CRITICAL FIX: Convert Naira amount to Kobo before adding to balance
            $fundingAmountInKobo = (float) $walletFunding->getAmount();
            $newBalance = (float) $wallet->getBalance() + $fundingAmountInKobo;
            $wallet->setBalance((string) $newBalance);
            $this->entityManager->persist($wallet);
        }

        $this->entityManager->flush();

        $this->logger->info("Wallet Funding Transaction has been queried from gateway successfully.");
    }
}