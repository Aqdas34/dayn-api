<?php

namespace App\Model\Request;

use Symfony\Component\Validator\Constraints as Assert;

class WalletDemoFundRequestDto
{
    #[Assert\NotBlank(message: 'Amount is required.')]
    #[Assert\Positive(message: 'Amount must be greater than 0.')]
    private int $amount;

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): WalletDemoFundRequestDto
    {
        $this->amount = $amount;
        return $this;
    }
}