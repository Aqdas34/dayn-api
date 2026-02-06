<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\UserWallet;
use App\Repository\UserRepository;
use App\Repository\UserWalletRepository;
use App\Service\BankService;
use App\Util\UidUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:load-seed-data',
    description: 'Add a short description for your command',
)]
class LoadSeedDataCommand extends Command
{
    public function __construct(
        private readonly UserRepository              $userRepository,
        private readonly UserWalletRepository        $userWalletRepository,
        private readonly EntityManagerInterface      $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher, private readonly BankService $bankService,
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
        $arg1 = $input->getArgument('arg1');

//        if ($arg1) {
//            $io->note(sprintf('You passed an argument: %s', $arg1));
//        }
//
//        if ($input->getOption('option1')) {
//            // ...
//        }

        $this->loadUserData();

        $io->success('Seeding complete!');

        return Command::SUCCESS;
    }

    private function loadUserData(): void
    {
        $this->userWalletRepository->deleteAllRecords();
        $this->userRepository->deleteAllRecords();

        $user = new User();
        $user
            ->setUid(UidUtils::generateUid())
            ->setFirstName('John Smith')
            ->setUsername('john.smith@gmail.com')
            ->setPassword($this->passwordHasher->hashPassword($user, 'john@321'));
        $this->entityManager->persist($user);

        $userWallet = (new UserWallet())
            ->setBalance(0)
            ->setUser($user);
        $this->entityManager->persist($userWallet);

        $banks = $this->bankService->listBanks();
        if (empty($banks)) {
            return;
        }
        $bank = $banks[0];

        $this->entityManager->flush();
    }
}
