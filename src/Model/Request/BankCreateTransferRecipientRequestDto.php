<?php

namespace App\Model\Request;

use Symfony\Component\Serializer\Attribute\SerializedName;

class BankCreateTransferRecipientRequestDto
{
    #[SerializedName('account_number')]
    private string $accountNumber;
    #[SerializedName('account_name')]
    private string $accountName;
    #[SerializedName('bank_code')]
    private string $bankCode;

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(string $accountNumber): BankCreateTransferRecipientRequestDto
    {
        $this->accountNumber = $accountNumber;
        return $this;
    }

    public function getAccountName(): string
    {
        return $this->accountName;
    }

    public function setAccountName(string $accountName): BankCreateTransferRecipientRequestDto
    {
        $this->accountName = $accountName;
        return $this;
    }

    public function getBankCode(): string
    {
        return $this->bankCode;
    }

    public function setBankCode(string $bankCode): BankCreateTransferRecipientRequestDto
    {
        $this->bankCode = $bankCode;
        return $this;
    }
}