<?php

namespace App\Model\Request;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class WalletInitiatePayoutRequestDto
{
    #[Assert\NotBlank(message: 'Amount is required.')]
    private int $amount;
    #[Assert\NotBlank(message: 'User Bank Account ID is required.')]
    #[SerializedName('user_bank_account_id')]
    private int $userBankAccountId;
    #[Assert\NotBlank(message: 'Narration is required.')]
    private string $narration;

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): WalletInitiatePayoutRequestDto
    {
        $this->amount = $amount;
        return $this;
    }

    public function getUserBankAccountId(): int
    {
        return $this->userBankAccountId;
    }

    public function setUserBankAccountId(int $userBankAccountId): WalletInitiatePayoutRequestDto
    {
        $this->userBankAccountId = $userBankAccountId;
        return $this;
    }

    public function getNarration(): string
    {
        return $this->narration;
    }

    public function setNarration(string $narration): WalletInitiatePayoutRequestDto
    {
        $this->narration = $narration;
        return $this;
    }
}