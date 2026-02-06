<?php

namespace App\Model\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ForgotPasswordInitiateOtpRequestDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Email is required!')]
        public string $username,
    )
    {
    }
}