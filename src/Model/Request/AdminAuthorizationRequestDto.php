<?php

namespace App\Model\Request;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class AdminAuthorizationRequestDto
{
    public function __construct(
        #[Assert\NotBlank(message: "Authorization Code is required!")]
        #[SerializedName('authorization_code')]
        public string $authorizationCode
    )
    {
    }
}