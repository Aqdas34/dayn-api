<?php

namespace App\Model\Request;

use App\Entity\PaymentTransactionalAccount;
use Symfony\Component\Serializer\Attribute\SerializedName;

class InitiateWalletFundingResponseDto
{
    private string $reference;
    private string $amount;
    #[SerializedName('authorization_url')]
    private ?string $authorizationUrl;

    #[SerializedName('account_number')]
    private ?string $accountNumber;

    #[SerializedName('account_name')]
    private ?string $accountName;

    #[SerializedName('bank_name')]
    private ?string $bankName;

    #[SerializedName('bank_code')]
    private ?string $bankCode;

    #[SerializedName('amount_to_pay')]
    private ?string $amountToPay;

    #[SerializedName('created_at')]
    private ?\DateTimeImmutable $createdAt;

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference): InitiateWalletFundingResponseDto
    {
        $this->reference = $reference;
        return $this;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): InitiateWalletFundingResponseDto
    {
        $this->amount = $amount;
        return $this;
    }

    public function getAuthorizationUrl(): ?string
    {
        return $this->authorizationUrl;
    }

    public function setAuthorizationUrl(?string $authorizationUrl): InitiateWalletFundingResponseDto
    {
        $this->authorizationUrl = $authorizationUrl;
        return $this;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(?string $accountNumber): InitiateWalletFundingResponseDto
    {
        $this->accountNumber = $accountNumber;
        return $this;
    }

    public function getAccountName(): ?string
    {
        return $this->accountName;
    }

    public function setAccountName(?string $accountName): InitiateWalletFundingResponseDto
    {
        $this->accountName = $accountName;
        return $this;
    }

    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    public function setBankName(?string $bankName): InitiateWalletFundingResponseDto
    {
        $this->bankName = $bankName;
        return $this;
    }

    public function getBankCode(): ?string
    {
        return $this->bankCode;
    }

    public function setBankCode(?string $bankCode): InitiateWalletFundingResponseDto
    {
        $this->bankCode = $bankCode;
        return $this;
    }

    public function getAmountToPay(): ?string
    {
        return $this->amountToPay;
    }

    public function setAmountToPay(?string $amountToPay): InitiateWalletFundingResponseDto
    {
        $this->amountToPay = $amountToPay;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): InitiateWalletFundingResponseDto
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public static function fromPaymentTransactionalAccount(PaymentTransactionalAccount $transactionalAccount): InitiateWalletFundingResponseDto
    {
        return (new self())
            ->setReference($transactionalAccount->getTransactionReference())
            ->setAmount($transactionalAccount->getAmountSpecified())
            ->setAmountToPay($transactionalAccount->getAmountCharged())
            ->setAccountName($transactionalAccount->getAccountName())
            ->setAccountNumber($transactionalAccount->getAccountNumber())
            ->setBankName($transactionalAccount->getBankName())
            ->setBankCode($transactionalAccount->getBankCode())
            ->setCreatedAt($transactionalAccount->getCreatedAt());
    }
}