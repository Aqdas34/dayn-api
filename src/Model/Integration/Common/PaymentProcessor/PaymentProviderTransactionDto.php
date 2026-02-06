<?php

namespace App\Model\Integration\Common\PaymentProcessor;

class PaymentProviderTransactionDto
{
    private float $amount;
    private float $amountPaid;
    private string $transactionReference;
    private string $transactionStatus;
    private bool $appliedStampDutyFee;
    private float $stampDutyFee;
    private string $createdAt;
    private string $completedAt;

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): PaymentProviderTransactionDto
    {
        $this->amount = $amount;
        return $this;
    }

    public function getAmountPaid(): float
    {
        return $this->amountPaid;
    }

    public function setAmountPaid(float $amountPaid): PaymentProviderTransactionDto
    {
        $this->amountPaid = $amountPaid;
        return $this;
    }

    public function getTransactionReference(): string
    {
        return $this->transactionReference;
    }

    public function setTransactionReference(string $transactionReference): PaymentProviderTransactionDto
    {
        $this->transactionReference = $transactionReference;
        return $this;
    }

    public function getTransactionStatus(): string
    {
        return $this->transactionStatus;
    }

    public function setTransactionStatus(string $transactionStatus): PaymentProviderTransactionDto
    {
        $this->transactionStatus = $transactionStatus;
        return $this;
    }

    public function isAppliedStampDutyFee(): bool
    {
        return $this->appliedStampDutyFee;
    }

    public function setAppliedStampDutyFee(bool $appliedStampDutyFee): PaymentProviderTransactionDto
    {
        $this->appliedStampDutyFee = $appliedStampDutyFee;
        return $this;
    }

    public function getStampDutyFee(): float
    {
        return $this->stampDutyFee;
    }

    public function setStampDutyFee(float $stampDutyFee): PaymentProviderTransactionDto
    {
        $this->stampDutyFee = $stampDutyFee;
        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): PaymentProviderTransactionDto
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCompletedAt(): string
    {
        return $this->completedAt;
    }

    public function setCompletedAt(string $completedAt): PaymentProviderTransactionDto
    {
        $this->completedAt = $completedAt;
        return $this;
    }
}