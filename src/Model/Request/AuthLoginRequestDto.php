<?php

namespace App\Model\Request;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class AuthLoginRequestDto
{
    public function __construct(
        #[Assert\NotBlank(message: "Username is required.")]
        public string $username,
        #[Assert\NotBlank(message: "Password is required.")]
        public string $password,
        #[Assert\NotBlank(message: "Device UID is required.")]
        #[SerializedName("device_uid")]
        public string $deviceUid,
    ) {}
}