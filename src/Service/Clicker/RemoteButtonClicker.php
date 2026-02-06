<?php

namespace App\Service\Clicker;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator(ButtonClicker::class)]
class RemoteButtonClicker implements Clicker
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Clicker $inner
    )
    {

    }

    public function click(): void
    {
        $this->logger->info("About to click on the button...");

        $this->inner->click();

        $this->logger->info("Successfully clicked on the button...");
    }
}