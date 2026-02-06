<?php

namespace App\Service;

use App\Enum\WalletFundingStatus;
use App\Enum\WalletPayoutStatus;
use App\Repository\WalletFundingRepository;
use App\Repository\WalletPayoutRepository;

readonly class BackgroundJobService
{
    public function __construct(
        private WalletPayoutService     $walletPayoutService,
        private WalletPayoutRepository  $walletPayoutRepository,
        private WalletFundingRepository $walletFundingRepository,
        private WalletFundingService    $walletFundingService,
    )
    {
    }

    public function processPendingWalletPayouts(): void
    {
        $pendingWalletPayouts = $this->walletPayoutRepository->findBy([
            'status' => WalletPayoutStatus::QUEUED,
        ]);

        foreach ($pendingWalletPayouts as $walletPayout) {
            $this->walletPayoutService->processQueuedWalletPayout($walletPayout);
        }
    }

    public function processProcessingWalletFunding(): void
    {
        $processingWalletFunding = $this->walletFundingRepository->findBy([
            'status' => WalletFundingStatus::PROCESSING,
        ]);

        foreach ($processingWalletFunding as $walletFunding) {
            $this->walletFundingService->processProcessingWalletFunding($walletFunding);
        }
    }

    public function processProcessingWalletPayouts(): void
    {
        $processingWalletPayouts = $this->walletPayoutRepository->findBy([
            'status' => WalletPayoutStatus::PROCESSING,
        ]);

        foreach ($processingWalletPayouts as $walletPayout) {
            $this->walletPayoutService->processProcessingWalletPayout($walletPayout);
        }
    }
}