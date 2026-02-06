<?php

namespace App\Entity;

use App\Enum\DaynTransactionalAccountFeeType;
use App\Model\Integration\Common\PaymentProvider;
use App\Repository\PaymentTransactionalAccountRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentTransactionalAccountRepository::class)]
class PaymentTransactionalAccount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $uid = null;

    #[ORM\Column(length: 20)]
    private ?string $accountNumber = null;

    #[ORM\Column(length: 200)]
    private ?string $accountName = null;

    #[ORM\Column(length: 200)]
    private ?string $customerName = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $amountSpecified = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $amountCharged = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $fee = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $feeRate = null;

    #[ORM\Column(enumType: DaynTransactionalAccountFeeType::class)]
    private ?DaynTransactionalAccountFeeType $feeType = null;

    #[ORM\Column(enumType: PaymentProvider::class)]
    private ?PaymentProvider $provider = null;

    #[ORM\Column(length: 50)]
    private ?string $providerReference = null;

    #[ORM\Column(length: 50)]
    private ?string $transactionReference = null;

    #[ORM\Column]
    private array $providerResponseObject = [];

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastModifiedAt = null;

    #[ORM\Column(length: 200)]
    private ?string $bankName = null;

    #[ORM\Column(length: 20)]
    private ?string $bankCode = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(string $uid): static
    {
        $this->uid = $uid;

        return $this;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(string $accountNumber): static
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    public function getAccountName(): ?string
    {
        return $this->accountName;
    }

    public function setAccountName(string $accountName): static
    {
        $this->accountName = $accountName;

        return $this;
    }

    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    public function setCustomerName(string $customerName): static
    {
        $this->customerName = $customerName;

        return $this;
    }

    public function getAmountSpecified(): ?string
    {
        return $this->amountSpecified;
    }

    public function setAmountSpecified(string $amountSpecified): static
    {
        $this->amountSpecified = $amountSpecified;

        return $this;
    }

    public function getAmountCharged(): ?string
    {
        return $this->amountCharged;
    }

    public function setAmountCharged(string $amountCharged): static
    {
        $this->amountCharged = $amountCharged;

        return $this;
    }

    public function getFee(): ?string
    {
        return $this->fee;
    }

    public function setFee(string $fee): static
    {
        $this->fee = $fee;

        return $this;
    }

    public function getFeeRate(): ?string
    {
        return $this->feeRate;
    }

    public function setFeeRate(string $feeRate): static
    {
        $this->feeRate = $feeRate;

        return $this;
    }

    public function getFeeType(): ?DaynTransactionalAccountFeeType
    {
        return $this->feeType;
    }

    public function setFeeType(DaynTransactionalAccountFeeType $feeType): static
    {
        $this->feeType = $feeType;

        return $this;
    }

    public function getProvider(): ?PaymentProvider
    {
        return $this->provider;
    }

    public function setProvider(PaymentProvider $provider): static
    {
        $this->provider = $provider;

        return $this;
    }

    public function getProviderReference(): ?string
    {
        return $this->providerReference;
    }

    public function setProviderReference(string $providerReference): static
    {
        $this->providerReference = $providerReference;

        return $this;
    }

    public function getTransactionReference(): ?string
    {
        return $this->transactionReference;
    }

    public function setTransactionReference(string $transactionReference): static
    {
        $this->transactionReference = $transactionReference;

        return $this;
    }

    public function getProviderResponseObject(): array
    {
        return $this->providerResponseObject;
    }

    public function setProviderResponseObject(array $providerResponseObject): static
    {
        $this->providerResponseObject = $providerResponseObject;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getLastModifiedAt(): ?\DateTimeImmutable
    {
        return $this->lastModifiedAt;
    }

    public function setLastModifiedAt(\DateTimeImmutable $lastModifiedAt): static
    {
        $this->lastModifiedAt = $lastModifiedAt;

        return $this;
    }

    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    public function setBankName(string $bankName): static
    {
        $this->bankName = $bankName;

        return $this;
    }

    public function getBankCode(): ?string
    {
        return $this->bankCode;
    }

    public function setBankCode(string $bankCode): static
    {
        $this->bankCode = $bankCode;

        return $this;
    }
}
