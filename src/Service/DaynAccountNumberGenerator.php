<?php

namespace App\Service;

use App\Repository\AccountNumberSequenceRepository;

class DaynAccountNumberGenerator
{
    private const PREFIX = "888";

    public function __construct(
        private readonly AccountNumberSequenceRepository $repository
    )
    {
    }

    public function generate(): string
    {
        if (strlen(self::PREFIX) !== 3) {
            throw new \InvalidArgumentException('Prefix must be exactly 3 digits.');
        }

        return $this->repository->getNextAccountNumber(self::PREFIX);
    }
}