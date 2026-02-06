<?php

namespace App\Model\DTO;

use Symfony\Component\Serializer\Attribute\SerializedName;

class InitiatePspPaymentResponseDto
{
    #[SerializedName('authorization_url')]
    private ?string $authorizationUrl;
    #[SerializedName('access_code')]
    private string $accessCode;
    private string $reference;
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
    private string $createdAt;

    public function __construct(
        ?string $authorizationUrl,
        string $accessCode,
        string $reference,
        ?string $accountNumber = null,
        ?string $accountName = null,
        ?string $bankName = null,
        ?string $bankCode = null,
        ?string $amountToPay = null,
        ?string $createdAt = null
    ) {
        $this->authorizationUrl = $authorizationUrl;
        $this->accessCode = $accessCode;
        $this->reference = $reference;
        $this->accountNumber = $accountNumber;
        $this->accountName = $accountName;
        $this->bankName = $bankName;
        $this->bankCode = $bankCode;
        $this->amountToPay = $amountToPay;
        $this->createdAt = $createdAt ?? (new \DateTime())->format(\DateTime::RFC3339);
    }

    public function getAuthorizationUrl(): ?string
    {
        return $this->authorizationUrl;
    }

    public function getAccessCode(): string
    {
        return $this->accessCode;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function getAccountName(): ?string
    {
        return $this->accountName;
    }

    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    public function getBankCode(): ?string
    {
        return $this->bankCode;
    }

    public function getAmountToPay(): ?string
    {
        return $this->amountToPay;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}

