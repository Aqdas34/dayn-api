<?php

namespace App\Model\Integration\Monnify;

class MonnifyCreateReservedAccountResponseDto
{
    private string $contractCode = '';
    private string $accountReference = '';
    private string $accountName = '';
    private string $customerEmail = '';
    private string $customerName = '';
    private array $accounts = []; // Array of bank details
    private string $accountNumber = '';
    private string $bankName = '';
    private string $bankCode = '';

    public function getContractCode(): string
    {
        return $this->contractCode;
    }

    public function setContractCode(string $contractCode): self
    {
        $this->contractCode = $contractCode;
        return $this;
    }

    public function getAccountReference(): string
    {
        return $this->accountReference;
    }

    public function setAccountReference(string $accountReference): self
    {
        $this->accountReference = $accountReference;
        return $this;
    }

    public function getAccountName(): string
    {
        return $this->accountName;
    }

    public function setAccountName(string $accountName): self
    {
        $this->accountName = $accountName;
        return $this;
    }

    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    public function setCustomerEmail(string $customerEmail): self
    {
        $this->customerEmail = $customerEmail;
        return $this;
    }

    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    public function setCustomerName(string $customerName): self
    {
        $this->customerName = $customerName;
        return $this;
    }

    public function getAccounts(): array
    {
        return $this->accounts;
    }

    public function setAccounts(array $accounts): self
    {
        $this->accounts = $accounts;
        return $this;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(string $accountNumber): self
    {
        $this->accountNumber = $accountNumber;
        return $this;
    }

    public function getBankName(): string
    {
        return $this->bankName;
    }

    public function setBankName(string $bankName): self
    {
        $this->bankName = $bankName;
        return $this;
    }

    public function getBankCode(): string
    {
        return $this->bankCode;
    }

    public function setBankCode(string $bankCode): self
    {
        $this->bankCode = $bankCode;
        return $this;
    }
}
