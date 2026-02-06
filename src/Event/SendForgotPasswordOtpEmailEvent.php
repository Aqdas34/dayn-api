<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class SendForgotPasswordOtpEmailEvent extends Event
{
    public function __construct(
        public readonly int $forgotPasswordTokenId,
    )
    {
    }
}