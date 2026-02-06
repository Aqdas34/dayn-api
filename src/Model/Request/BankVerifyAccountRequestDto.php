<?php

namespace App\Model\Request;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class BankVerifyAccountRequestDto
{
    #[Assert\NotBlank(message: 'Account Number is required.')]
    private string $accountNumber;
    #[Assert\NotBlank(message: 'Bank Code is required.')]
    private string $bankCode;

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(string $accountNumber): BankVerifyAccountRequestDto
    {
        $this->accountNumber = $accountNumber;
        return $this;
    }

    public function getBankCode(): string
    {
        return $this->bankCode;
    }

    public function setBankCode(string $bankCode): BankVerifyAccountRequestDto
    {
        $this->bankCode = $bankCode;
        return $this;
    }
}