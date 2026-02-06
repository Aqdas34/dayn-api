<?php

namespace App\Model\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ForgotPasswordVerifyOtpRequestDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Email is required!')]
        public string $username,
        #[Assert\NotBlank(message: 'OTP is required!')]
        public string $otp,
    )
    {
    }
}