<?php

namespace App\Service\Clicker;

use App\Util\DateTimeUtils;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

#[AsAlias]
class ButtonClicker implements Clicker
{
    public function __construct(private readonly LoggerInterface $logger)
    {
        
    }

    public function click(): void
    {
        $this->logger->info("Clicked on a button @ {time}", [
            'time' => DateTimeUtils::getDateTimeNow()->format('Y-m-d H:i:s'),
        ]);
    }
}