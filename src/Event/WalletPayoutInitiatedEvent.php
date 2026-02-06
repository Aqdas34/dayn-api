<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class WalletPayoutInitiatedEvent extends Event
{
    public function __construct(
        private readonly int $walletPayoutId
    )
    {
    }

    /**
     * @return string
     */
    public function getWalletPayoutId(): string
    {
        return $this->walletPayoutId;
    }
}