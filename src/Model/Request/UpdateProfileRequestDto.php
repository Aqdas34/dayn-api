<?php

namespace App\Model\Request;

use Symfony\Component\Serializer\Attribute\SerializedName;

readonly class UpdateProfileRequestDto
{
    public function __construct(
        #[SerializedName("first_name")]
        public string $firstName,
        #[SerializedName("last_name")]
        public string $lastName,
        public string $email
    )
    {
    }
}