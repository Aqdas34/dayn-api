<?php

namespace App\Model\Request;

use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class InviteUserByPhoneNumberRequestDto
{
    public function __construct(
        #[SerializedName('phone_number')]
        public string $phoneNumber,
        public array $context = [],
    )
    {
    }
}