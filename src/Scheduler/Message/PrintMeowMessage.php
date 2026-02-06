<?php

namespace App\Scheduler\Message;

readonly class PrintMeowMessage
{
    public function __construct(
        public string $message
    )
    {
    }
}