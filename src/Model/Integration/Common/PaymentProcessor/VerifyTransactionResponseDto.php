<?php

namespace App\Model\Integration\Common\PaymentProcessor;

class VerifyTransactionResponseDto
{
    private string $providerTransactionId;
    private string $transactionReference;
    private float $amountSpecified;
    private float $amountPaid;
    private float $fee;
    private float $vat;
    private float $stampDutyFee;
    private bool $appliedStampDutyFee;
    private string $currency;
    private GenericPaymentProcessorTransactionStatus $transactionStatus;
    private string $paymentStartDate;
    private ?string $paymentCompletionDate = null;

    public function getProviderTransactionId(): string
    {
        return $this->providerTransactionId;
    }

    public function setProviderTransactionId(string $providerTransactionId): VerifyTransactionResponseDto
    {
        $this->providerTransactionId = $providerTransactionId;
        return $this;
    }

    public function getTransactionReference(): string
    {
        return $this->transactionReference;
    }

    public function setTransactionReference(string $transactionReference): VerifyTransactionResponseDto
    {
        $this->transactionReference = $transactionReference;
        return $this;
    }

    public function getAmountSpecified(): float
    {
        return $this->amountSpecified;
    }

    public function setAmountSpecified(float $amountSpecified): VerifyTransactionResponseDto
    {
        $this->amountSpecified = $amountSpecified;
        return $this;
    }

    public function getAmountPaid(): float
    {
        return $this->amountPaid;
    }

    public function setAmountPaid(float $amountPaid): VerifyTransactionResponseDto
    {
        $this->amountPaid = $amountPaid;
        return $this;
    }

    public function getFee(): float
    {
        return $this->fee;
    }

    public function setFee(float $fee): VerifyTransactionResponseDto
    {
        $this->fee = $fee;
        return $this;
    }

    public function getVat(): float
    {
        return $this->vat;
    }

    public function setVat(float $vat): VerifyTransactionResponseDto
    {
        $this->vat = $vat;
        return $this;
    }

    public function getStampDutyFee(): float
    {
        return $this->stampDutyFee;
    }

    public function setStampDutyFee(float $stampDutyFee): VerifyTransactionResponseDto
    {
        $this->stampDutyFee = $stampDutyFee;
        return $this;
    }

    public function isAppliedStampDutyFee(): bool
    {
        return $this->appliedStampDutyFee;
    }

    public function setAppliedStampDutyFee(bool $appliedStampDutyFee): VerifyTransactionResponseDto
    {
        $this->appliedStampDutyFee = $appliedStampDutyFee;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): VerifyTransactionResponseDto
    {
        $this->currency = $currency;
        return $this;
    }

    public function getTransactionStatus(): GenericPaymentProcessorTransactionStatus
    {
        return $this->transactionStatus;
    }

    public function setTransactionStatus(GenericPaymentProcessorTransactionStatus $transactionStatus): VerifyTransactionResponseDto
    {
        $this->transactionStatus = $transactionStatus;
        return $this;
    }

    public function getPaymentStartDate(): string
    {
        return $this->paymentStartDate;
    }

    public function setPaymentStartDate(string $paymentStartDate): VerifyTransactionResponseDto
    {
        $this->paymentStartDate = $paymentStartDate;
        return $this;
    }

    public function getPaymentCompletionDate(): ?string
    {
        return $this->paymentCompletionDate;
    }

    public function setPaymentCompletionDate(?string $paymentCompletionDate): VerifyTransactionResponseDto
    {
        $this->paymentCompletionDate = $paymentCompletionDate;
        return $this;
    }
}