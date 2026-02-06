<?php

namespace App\Model\Integration\Common\PaymentProcessor;

class CreateTransactionalAccountResponseDto
{
    private string $accountNumber;
    private string $accountName;
    private string $bankName;
    private string $bankCode;
    private string $currency;
    private string $amount;
    private string $providerAccountReference;
    private string $createdAt;

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(string $accountNumber): CreateTransactionalAccountResponseDto
    {
        $this->accountNumber = $accountNumber;
        return $this;
    }

    public function getAccountName(): string
    {
        return $this->accountName;
    }

    public function setAccountName(string $accountName): CreateTransactionalAccountResponseDto
    {
        $this->accountName = $accountName;
        return $this;
    }

    public function getBankName(): string
    {
        return $this->bankName;
    }

    public function setBankName(string $bankName): CreateTransactionalAccountResponseDto
    {
        $this->bankName = $bankName;
        return $this;
    }

    public function getBankCode(): string
    {
        return $this->bankCode;
    }

    public function setBankCode(string $bankCode): CreateTransactionalAccountResponseDto
    {
        $this->bankCode = $bankCode;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): CreateTransactionalAccountResponseDto
    {
        $this->currency = $currency;
        return $this;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): CreateTransactionalAccountResponseDto
    {
        $this->amount = $amount;
        return $this;
    }

    public function getProviderAccountReference(): string
    {
        return $this->providerAccountReference;
    }

    public function setProviderAccountReference(string $providerAccountReference): CreateTransactionalAccountResponseDto
    {
        $this->providerAccountReference = $providerAccountReference;
        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): CreateTransactionalAccountResponseDto
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}