<?php

namespace App\Util;

use Symfony\Component\PasswordHasher\Hasher\SodiumPasswordHasher;

class HashingUtil
{
    private SodiumPasswordHasher $hasher;
    public function __construct()
    {
        $this->hasher = new SodiumPasswordHasher();
    }

    public function hash(string $value): string
    {
        return $this->hasher->hash($value);
    }

    public function verify(string $hashedValue, string $value): bool
    {
        return $this->hasher->verify($hashedValue, $value);
    }
}