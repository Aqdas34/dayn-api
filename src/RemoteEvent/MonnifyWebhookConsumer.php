<?php

namespace App\RemoteEvent;

use App\Exception\Api\NotFoundException;
use App\Model\Integration\Monnify\MonnifyWebhookEventType;
use App\Repository\PaymentTransactionalAccountRepository;
use App\Repository\WalletFundingRepository;
use App\Repository\WalletPayoutRepository;
use App\Service\DebtCollectionPaymentService;
use App\Service\WalletFundingService;
use App\Service\WalletPayoutService;
use Psr\Log\LoggerInterface;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

#[AsRemoteEventConsumer('monnify')]
final readonly class MonnifyWebhookConsumer implements ConsumerInterface
{
    public function __construct(
        private WalletPayoutService     $walletPayoutService,
        private WalletPayoutRepository  $walletPayoutRepository,
        private WalletFundingService    $walletFundingService,
        private WalletFundingRepository $walletFundingRepository,
        private DebtCollectionPaymentService $debtCollectionPaymentService,
        private PaymentTransactionalAccountRepository $paymentTransactionalAccountRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function consume(RemoteEvent $event): void
    {
        $payload = $event->getPayload();
        $eventType = $payload['eventType'];
        $eventData = $payload['eventData'];

        $this->logger->info("Monnify Event: $eventType");

        match (MonnifyWebhookEventType::tryFrom($eventType)) {
            MonnifyWebhookEventType::SUCCESSFUL_TRANSACTION => $this->handleMonnifySuccess($eventData),
            MonnifyWebhookEventType::TRANSFER_SUCCESSFUL,
            MonnifyWebhookEventType::TRANSFER_FAILED,
            MonnifyWebhookEventType::TRANSFER_REVERSED => $this->handleMonnifyTransfer($eventData),
            default => $this->logger->error("No handler for Monnify event: $eventType"),
        };
    }

    private function handleMonnifySuccess(array $eventData): void
    {
        $transactionReference = $eventData['paymentReference'];
        $this->logger->info("Processing Monnify Success for: $transactionReference");

        // Try Wallet Funding first
        $walletFunding = $this->walletFundingRepository->findOneBy(['reference' => $transactionReference]);
        if ($walletFunding) {
            $this->logger->info("Found Wallet Funding for reference: $transactionReference");
            $this->walletFundingService->processProcessingWalletFunding($walletFunding);
            return;
        }

        // Try Debt Collection Payment
        $paymentAccount = $this->paymentTransactionalAccountRepository->findOneBy(['transactionReference' => $transactionReference]);
        if ($paymentAccount) {
            $this->logger->info("Found Debt Collection Payment for reference: $transactionReference");
            $this->debtCollectionPaymentService->processDebtPayment($transactionReference);
            return;
        }

        $this->logger->error("No transaction found for reference: $transactionReference");
    }

    private function handleMonnifyTransfer(array $eventData): void
    {
        $transactionReference = $eventData['reference'];
        $this->logger->info("Processing Monnify Transfer for: $transactionReference");

        $walletPayout = $this->walletPayoutRepository->findOneBy(['reference' => $transactionReference]);
        if (!$walletPayout) {
            $this->logger->error("Wallet Payout not found for reference: $transactionReference");
            return;
        }

        $this->walletPayoutService->processProcessingWalletPayout($walletPayout);
    }
}
