<?php

namespace App\Model\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ProfileUpdateTransactionPinRequestDto
{
    public function __construct(
        #[Assert\NotBlank(message: "Current PIN must not be blank")]
        #[Assert\Length(min: 4, max: 4)]
        #[Assert\Positive(message: "Current PIN must be a digit!")]
        public string $currentPin,
        #[Assert\NotBlank(message: "New PIN must not be blank")]
        #[Assert\Length(min: 4, max: 4)]
        #[Assert\Positive(message: "New PIN must be a digit!")]
        public string $newPin,
    )
    {
    }
}