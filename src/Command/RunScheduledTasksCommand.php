<?php

namespace App\Command;

use App\Enum\WalletFundingStatus;
use App\Enum\WalletPayoutStatus;
use App\Repository\WalletFundingRepository;
use App\Repository\WalletPayoutRepository;
use App\Service\WalletFundingService;
use App\Service\WalletPayoutService;
use App\Util\DateTimeUtils;
use App\Util\UidUtils;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:run-scheduled-tasks',
    description: 'Add a short description for your command',
)]
class RunScheduledTasksCommand extends Command
{
    public function __construct(
        private readonly WalletFundingRepository $walletFundingRepository,
        private readonly WalletFundingService $walletFundingService,
        private readonly WalletPayoutService $walletPayoutService,
        private readonly WalletPayoutRepository $walletPayoutRepository,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('time-limit', InputArgument::OPTIONAL, 'The time duration in seconds for which the command should run...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $timeLimit = $input->getArgument('time-limit');
        $durationInSeconds = 55;
        $defaultSleepTimeout = 30;

        if ($timeLimit) {
            $durationInSeconds = intval($timeLimit);
            $io->note(sprintf('You passed a time limit: %s', $timeLimit));
        }

        $stopAppAt = DateTimeUtils::getDateTimeNow()->modify("+$durationInSeconds seconds");

        while(true) {
            if (DateTimeUtils::hasDateTimeElapsed($stopAppAt)) {
                break;
            }
            $io->info('Execute checking queued payouts!');
            $this->processPendingWalletPayouts();
            $io->info('Execute checking processing payouts!');
            $this->processProcessingWalletPayouts();
            $io->info('Execute checking pending transactions!');
            $this->processProcessingWalletFunding();
            sleep($defaultSleepTimeout);
        }

        return Command::SUCCESS;
    }

    private function processPendingWalletPayouts(): void
    {
        $pendingWalletPayouts = $this->walletPayoutRepository->findBy([
            'status' => WalletPayoutStatus::QUEUED,
        ]);

        foreach ($pendingWalletPayouts as $walletPayout) {
            $this->walletPayoutService->processQueuedWalletPayout($walletPayout);
        }
    }

    private function processProcessingWalletFunding(): void
    {
        $processingWalletFunding = $this->walletFundingRepository->findBy([
            'status' => WalletFundingStatus::PROCESSING,
        ]);

        foreach ($processingWalletFunding as $walletFunding) {
            $this->walletFundingService->processProcessingWalletFunding($walletFunding);
        }
    }

    private function processProcessingWalletPayouts(): void
    {
        $processingWalletPayouts = $this->walletPayoutRepository->findBy([
            'status' => WalletPayoutStatus::PROCESSING,
        ]);

        foreach ($processingWalletPayouts as $walletPayout) {
            $this->walletPayoutService->processProcessingWalletPayout($walletPayout);
        }
    }
}
