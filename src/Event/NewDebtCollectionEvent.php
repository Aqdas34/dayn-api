<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class NewDebtCollectionEvent extends Event
{
    public function __construct(
        public readonly string $debtCollectionUid
    )
    {
    }
}