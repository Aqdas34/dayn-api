<?php

namespace App\Model\Request;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class AddBankAccountRequestDto
{
    #[Assert\NotBlank(message: 'Account Number is required.')]
    #[SerializedName('account_number')]
    private string $accountNumber;

    #[Assert\NotBlank(message: 'Account Name is required.')]
    #[SerializedName('account_name')]
    private string $accountName;

    #[Assert\NotBlank(message: 'Bank Name is required.')]
    #[SerializedName('bank_name')]
    private string $bankName;

    #[Assert\NotBlank(message: 'Bank Code is required.')]
    #[SerializedName('bank_code')]
    private string $bankCode;

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(string $accountNumber): AddBankAccountRequestDto
    {
        $this->accountNumber = $accountNumber;
        return $this;
    }

    public function getAccountName(): string
    {
        return $this->accountName;
    }

    public function setAccountName(string $accountName): AddBankAccountRequestDto
    {
        $this->accountName = $accountName;
        return $this;
    }

    public function getBankName(): string
    {
        return $this->bankName;
    }

    public function setBankName(string $bankName): AddBankAccountRequestDto
    {
        $this->bankName = $bankName;
        return $this;
    }

    public function getBankCode(): string
    {
        return $this->bankCode;
    }

    public function setBankCode(string $bankCode): AddBankAccountRequestDto
    {
        $this->bankCode = $bankCode;
        return $this;
    }
}