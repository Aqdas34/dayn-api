<?php

namespace App\Model\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ProfileSetTransactionPinRequestDto
{
    public function __construct(
        #[Assert\NotBlank(message: "PIN must not be blank")]
        #[Assert\Length(min: 4, max: 4)]
        #[Assert\Positive(message: "PIN must be a digit!")]
        public string $pin
    )
    {
    }
}