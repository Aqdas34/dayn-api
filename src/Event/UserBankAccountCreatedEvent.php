<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class UserBankAccountCreatedEvent extends Event
{
    public function __construct(
        private readonly int $userBankAccountId,
    )
    {
    }

    /**
     * @return integer
     */
    public function getUserBankAccountId(): int
    {
        return $this->userBankAccountId;
    }
}