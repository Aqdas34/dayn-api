<?php

namespace App\Model\Request;

final readonly class TestPinVerifyHashRequestDto
{
    public function __construct(
        public string $pin,
        public string $hash,
    )
    {
    }
}