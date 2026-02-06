<?php

namespace App\Model\Request;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class AuthAccessTokenRequestDto
{
    public function __construct(
        #[Assert\NotBlank(message: "Device UID is required!")]
        #[SerializedName('device_uid')]
        public string $deviceUid
    ) {}
}