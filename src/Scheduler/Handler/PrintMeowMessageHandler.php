<?php

namespace App\Scheduler\Handler;

use App\Scheduler\Message\PrintMeowMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class PrintMeowMessageHandler
{
    public function __construct(
        private LoggerInterface $logger
    )
    {
    }


    public function __invoke(PrintMeowMessage $message): void
    {
        $this->logger->info("About to print meow...");
        $this->logger->error("Message: {$message->message}");
        $this->logger->info("Finished printing meow...");
    }
}