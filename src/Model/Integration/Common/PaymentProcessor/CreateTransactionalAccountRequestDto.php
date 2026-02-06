<?php

namespace App\Model\Integration\Common\PaymentProcessor;

final class CreateTransactionalAccountRequestDto
{
    private string $transactionReference;
    private string $customerName;
    private string $customerEmail;
    private float $amount;

    public function getTransactionReference(): string
    {
        return $this->transactionReference;
    }

    public function setTransactionReference(string $transactionReference): CreateTransactionalAccountRequestDto
    {
        $this->transactionReference = $transactionReference;
        return $this;
    }

    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    public function setCustomerName(string $customerName): CreateTransactionalAccountRequestDto
    {
        $this->customerName = $customerName;
        return $this;
    }

    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    public function setCustomerEmail(string $customerEmail): CreateTransactionalAccountRequestDto
    {
        $this->customerEmail = $customerEmail;
        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): CreateTransactionalAccountRequestDto
    {
        $this->amount = $amount;
        return $this;
    }
}