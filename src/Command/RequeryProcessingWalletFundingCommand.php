<?php

namespace App\Command;

use App\Entity\WalletFunding;
use App\Enum\WalletFundingStatus;
use App\Repository\WalletFundingRepository;
use App\Service\WalletFundingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:requery-processing-wallet-funding',
    description: 'Add a short description for your command',
)]
class RequeryProcessingWalletFundingCommand extends Command
{
    public function __construct(
        private readonly WalletFundingRepository $walletFundingRepository,
        private readonly WalletFundingService $walletFundingService,
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

        $this->processProcessingWalletFunding();

        $io->success('All processing wallet fundings have been re-queried successfully!');

        return Command::SUCCESS;
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
}
