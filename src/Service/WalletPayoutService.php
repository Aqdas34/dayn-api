<?php

namespace App\Service;

use App\Entity\WalletPayout;
use App\Enum\WalletPayoutStatus;
use App\Model\Integration\Paystack\PaystackInitiateTransferRequestDto;
use App\Model\Integration\Paystack\PaystackTransferStatus;
use App\Repository\UserWalletRepository;
use App\Repository\WalletPayoutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use App\Service\MonnifyService; // Added MonnifyService import
use App\Util\MoneyUtil;
use Doctrine\DBAL\LockMode;
use Symfony\Component\DependencyInjection\Attribute\Value; // Added Value attribute import

class WalletPayoutService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MonnifyService $monnifyService, // Changed from PaystackService
        private readonly UserWalletRepository $walletRepository,
        private readonly WalletPayoutRepository $walletPayoutRepository,
        private readonly LoggerInterface $logger,
        private readonly SerializerInterface $serializer,
        #[Value('%monnify_contract_code%')] private string $sourceAccountNumber, // Added new injected parameter
    )
    {
    }

    public function processQueuedWalletPayout(WalletPayout $walletPayout): void
    {
        if ($walletPayout->getStatus() !== WalletPayoutStatus::QUEUED) {
            $this->logger->error("Could not process the Payout with ID: {payoutId} as it is in an invalid state: {status}", [
                'payoutId' => $walletPayout->getId(),
                'status' => $walletPayout->getStatus(),
            ]);
            return;
        }

        $receivingBankAccount = $walletPayout->getReceivingBankAccount();

        // Monnify disbursement data
        // Monnify expects amount in Naira, but we store it in Kobo.
        $amountInNaira = (float)$walletPayout->getAmount() / 100;

        $disbursementData = [
            'amount' => $amountInNaira,
            'reference' => $walletPayout->getReference(),
            'narration' => $walletPayout->getNarration() ?? 'Wallet Payout',
            'destinationBankCode' => $receivingBankAccount->getBankCode(),
            'destinationAccountNumber' => $receivingBankAccount->getAccountNumber(),
            'currency' => 'NGN',
            'sourceAccountNumber' => $this->sourceAccountNumber,
        ];

        $initiateTransferResponse = $this->monnifyService->initiateDisbursement($disbursementData);
        if (!$initiateTransferResponse) {
            $walletPayout
                ->setStatus(WalletPayoutStatus::FAILED)
                ->setStatusMessage("Failed due to an error initiating request with gateway!");
            $this->entityManager->persist($walletPayout);
            $this->entityManager->flush();
            return;
        }

        $transferReference = $initiateTransferResponse['reference'];
        $status = $initiateTransferResponse['status'] ?? 'PENDING';
        $gatewayResponseObject = $initiateTransferResponse;

        $this->logger->info("Monnify Payout Sent!\nReference: {reference}.\nStatus: {status}.", [
            'reference' => $transferReference,
            'status' => $status,
        ]);

        $walletPayout
            ->setStatus(WalletPayoutStatus::PROCESSING)
            ->setStatusMessage("The Transfer is being processed by Monnify!")
            ->setGatewayReference($transferReference)
            ->setGatewayResponseObject($gatewayResponseObject);
        $this->entityManager->persist($walletPayout);
        $this->entityManager->flush();

        $this->logger->info("Payout Transaction has been sent to Monnify successfully.");
    }

    public function processProcessingWalletPayout(WalletPayout $walletPayout): void
    {
        if ($walletPayout->getStatus() !== WalletPayoutStatus::PROCESSING) {
            $this->logger->error("Could not process the Payout with ID: {payoutId} as it is in an invalid state: {status}", [
                'payoutId' => $walletPayout->getId(),
                'status' => $walletPayout->getStatus(),
            ]);
            return;
        }

        $reference = $walletPayout->getGatewayReference();
        $verifyTransferResponse = $this->monnifyService->verifyDisbursement($reference);
        if (!$verifyTransferResponse) {
            $this->logger->error("Failed to obtain status of payout transfer with reference: {reference}", [
                'reference' => $reference,
            ]);
            return;
        }

        $gatewayStatus = $verifyTransferResponse['status'] ?? 'PENDING';
        $gatewayResponseObject = $verifyTransferResponse;

        $this->logger->info("Monnify Payout Status Retrieved!\nReference: {reference}.\nStatus: {status}.", [
            'reference' => $reference,
            'status' => $gatewayStatus,
        ]);

        $walletPayoutStatus = match ($gatewayStatus) {
            'SUCCESS' => WalletPayoutStatus::SUCCESS,
            'FAILED', 'REVERSED' => WalletPayoutStatus::FAILED,
            default => WalletPayoutStatus::PROCESSING,
        };

        $walletPayoutMessage = match ($gatewayStatus) {
            'SUCCESS' => 'Transfer Successful',
            'FAILED' => 'Transfer Failed',
            'REVERSED' => 'Transfer Reversed',
            default => 'Transfer is being processed',
        };

        $this->logger->info("Wallet Payout with ID: {payoutId} has status: {status} from gateway!", [
            'payoutId' => $walletPayout->getId(),
            'status' => $gatewayStatus,
        ]);

        $walletPayout
            ->setStatus($walletPayoutStatus)
            ->setStatusMessage($walletPayoutMessage)
            ->setGatewayResponseObject($gatewayResponseObject);
        $this->entityManager->persist($walletPayout);

        if ($walletPayoutStatus === WalletPayoutStatus::FAILED || $gatewayStatus === 'REVERSED') {
            $wallet = $walletPayout->getWallet();
            
            // LOCKING: Prevent concurrent balance updates
            $this->entityManager->lock($wallet, LockMode::PESSIMISTIC_WRITE);
            $this->entityManager->refresh($wallet);

            // FIX: Removed * 100 as amounts are already stored in Kobo
            $refundAmountInKobo = MoneyUtil::add($walletPayout->getAmount(), $walletPayout->getTransactionFee());
            $newBalance = MoneyUtil::add($wallet->getBalance(), $refundAmountInKobo);
            
            $wallet->setBalance((string) $newBalance);
            $this->entityManager->persist($wallet);
        }

        $this->entityManager->flush();

        $this->logger->info("Payout Transaction has been queried from Monnify successfully.");
    }
}