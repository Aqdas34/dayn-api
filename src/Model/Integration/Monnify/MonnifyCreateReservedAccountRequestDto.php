<?php

namespace App\Model\Integration\Monnify;

class MonnifyCreateReservedAccountRequestDto
{
    private string $accountReference;
    private string $accountName;
    private string $currencyCode = 'NGN';
    private string $contractCode;
    private string $customerEmail;
    private string $customerName;
    private bool $getAllAvailableBanks = true;
    private bool $restrictPaymentToFixedAmount = false;
    private ?float $fixedAmount = null;

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

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(string $currencyCode): self
    {
        $this->currencyCode = $currencyCode;
        return $this;
    }

    public function getContractCode(): string
    {
        return $this->contractCode;
    }

    public function setContractCode(string $contractCode): self
    {
        $this->contractCode = $contractCode;
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

    public function isGetAllAvailableBanks(): bool
    {
        return $this->getAllAvailableBanks;
    }

    public function setGetAllAvailableBanks(bool $getAllAvailableBanks): self
    {
        $this->getAllAvailableBanks = $getAllAvailableBanks;
        return $this;
    }

    public function isRestrictPaymentToFixedAmount(): bool
    {
        return $this->restrictPaymentToFixedAmount;
    }

    public function setRestrictPaymentToFixedAmount(bool $restrictPaymentToFixedAmount): self
    {
        $this->restrictPaymentToFixedAmount = $restrictPaymentToFixedAmount;
        return $this;
    }

    public function getFixedAmount(): ?float
    {
        return $this->fixedAmount;
    }

    public function setFixedAmount(?float $fixedAmount): self
    {
        $this->fixedAmount = $fixedAmount;
        return $this;
    }
}
