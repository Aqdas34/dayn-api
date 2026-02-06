<?php

namespace App\Command;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'app:setup-project',
    description: 'Add a short description for your command',
)]
class SetupProjectCommand extends Command
{
    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly LoggerInterface $logger,
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

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $app = new Application($this->kernel);
        $app->setAutoExit(false);

        $inputs = [];
        $inputs[] = new ArrayInput(['command' => 'doctrine:database:drop']);
        $inputs[] = new ArrayInput(['command' => 'doctrine:database:create']);
        $inputs[] = new ArrayInput(['command' => 'doctrine:schema:update', '--force' => true]);

        $io->info("Setting project. Kindly hold on...");

        foreach ($inputs as $input) {
            $output = new BufferedOutput();
            $app->run($input, $output);

            $content = $output->fetch();
            $this->logger->info("Command Result: $content");
        }

        $io->success('Project setup completed!');

        return Command::SUCCESS;
    }
}
