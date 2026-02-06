<?php

namespace App\Model\DTO;

use App\Entity\UserBankAccount;
use Symfony\Component\Serializer\Attribute\SerializedName;

class UserBankAccountDto
{
    private int $id;
    #[SerializedName('account_number')]
    private string $accountNumber;
    #[SerializedName('account_name')]
    private string $accountName;
    #[SerializedName('bank_code')]
    private string $bankCode;
    #[SerializedName('bank_name')]
    private string $bankName;
    #[SerializedName('currency')]
    private string $currency;
    #[SerializedName('gateway_recipient_code')]
    private ?string $gatewayRecipientCode;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): UserBankAccountDto
    {
        $this->id = $id;
        return $this;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(string $accountNumber): UserBankAccountDto
    {
        $this->accountNumber = $accountNumber;
        return $this;
    }

    public function getAccountName(): string
    {
        return $this->accountName;
    }

    public function setAccountName(string $accountName): UserBankAccountDto
    {
        $this->accountName = $accountName;
        return $this;
    }

    public function getBankCode(): string
    {
        return $this->bankCode;
    }

    public function setBankCode(string $bankCode): UserBankAccountDto
    {
        $this->bankCode = $bankCode;
        return $this;
    }

    public function getBankName(): string
    {
        return $this->bankName;
    }

    public function setBankName(string $bankName): UserBankAccountDto
    {
        $this->bankName = $bankName;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): UserBankAccountDto
    {
        $this->currency = $currency;
        return $this;
    }

    public function getGatewayRecipientCode(): ?string
    {
        return $this->gatewayRecipientCode;
    }

    public function setGatewayRecipientCode(?string $gatewayRecipientCode): UserBankAccountDto
    {
        $this->gatewayRecipientCode = $gatewayRecipientCode;
        return $this;
    }

    public static function fromUserBankAccount(UserBankAccount $userBankAccount): self
    {
        return (new self())
            ->setId($userBankAccount->getId() ?? 0)
            ->setAccountNumber($userBankAccount->getAccountNumber() ?? '')
            ->setAccountName($userBankAccount->getAccountName() ?? '')
            ->setBankCode($userBankAccount->getBankCode() ?? '')
            ->setBankName($userBankAccount->getBankName() ?? '')
            ->setCurrency($userBankAccount->getCurrency() ?? '')
            ->setGatewayRecipientCode($userBankAccount->getGatewayRecipientCode());
    }
}