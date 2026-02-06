<?php

namespace App\Command;

use App\Enum\WalletPayoutStatus;
use App\Repository\WalletPayoutRepository;
use App\Service\WalletPayoutService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:process-queued-wallet-payouts',
    description: 'Add a short description for your command',
)]
class ProcessQueuedWalletPayoutsCommand extends Command
{
    public function __construct(
        private readonly WalletPayoutService $walletPayoutService,
        private readonly WalletPayoutRepository $walletPayoutRepository,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
//        $arg1 = $input->getArgument('arg1');
//
//        if ($arg1) {
//            $io->note(sprintf('You passed an argument: %s', $arg1));
//        }
//
//        if ($input->getOption('option1')) {
//            // ...
//        }

        $this->processPendingWalletPayouts();

        $io->success('All queued payouts have been processed successfully!');

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
}
