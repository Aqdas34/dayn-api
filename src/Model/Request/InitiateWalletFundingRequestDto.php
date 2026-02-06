<?php

namespace App\Model\Request;

use Symfony\Component\Validator\Constraints as Assert;

readonly class InitiateWalletFundingRequestDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Amount is required!')]
        #[Assert\Positive(message: 'Amount must be greater than zero!')]
        public int $amount,
    )
    {
    }
}