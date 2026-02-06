<?php

namespace App\Model\Request;

use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class CheckUserAvailabilityByPhoneRequestDto
{
    public function __construct(
        #[SerializedName('phone_number')]
        public string $phoneNumber,
    )
    {
    }
}