<?php

namespace App\Model\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ForgotPasswordFinalizeRequestDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Email is required!')]
        public string $username,
        #[Assert\NotBlank(message: 'OTP is required!')]
        public string $otp,
        #[Assert\NotBlank(message: 'Password is required!')]
        public string $password,
    )
    {
    }
}