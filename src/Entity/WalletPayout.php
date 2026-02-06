<?php

namespace App\Entity;

use App\Enum\WalletPayoutStatus;
use App\Repository\WalletPayoutRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WalletPayoutRepository::class)]
class WalletPayout
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $uid = null;

    #[ORM\Column(length: 50)]
    private ?string $reference = null;

    #[ORM\ManyToOne(inversedBy: 'walletPayouts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserWallet $wallet = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $amount = null;

    #[ORM\Column(enumType: WalletPayoutStatus::class)]
    private ?WalletPayoutStatus $status = null;

    #[ORM\Column(length: 50)]
    private ?string $narration = null;

    #[ORM\ManyToOne(inversedBy: 'walletPayouts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserBankAccount $receivingBankAccount = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $gatewayReference = null;

    #[ORM\Column(nullable: true)]
    private ?array $gatewayResponseObject = null;

    #[ORM\Column(length: 100)]
    private ?string $statusMessage = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastModifiedAt = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $transactionFee = null;

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

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getWallet(): ?UserWallet
    {
        return $this->wallet;
    }

    public function setWallet(?UserWallet $wallet): static
    {
        $this->wallet = $wallet;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getStatus(): ?WalletPayoutStatus
    {
        return $this->status;
    }

    public function setStatus(WalletPayoutStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getNarration(): ?string
    {
        return $this->narration;
    }

    public function setNarration(string $narration): static
    {
        $this->narration = $narration;

        return $this;
    }

    public function getReceivingBankAccount(): ?UserBankAccount
    {
        return $this->receivingBankAccount;
    }

    public function setReceivingBankAccount(?UserBankAccount $receivingBankAccount): static
    {
        $this->receivingBankAccount = $receivingBankAccount;

        return $this;
    }

    public function getGatewayReference(): ?string
    {
        return $this->gatewayReference;
    }

    public function setGatewayReference(?string $gatewayReference): static
    {
        $this->gatewayReference = $gatewayReference;

        return $this;
    }

    public function getGatewayResponseObject(): ?array
    {
        return $this->gatewayResponseObject;
    }

    public function setGatewayResponseObject(?array $gatewayResponseObject): static
    {
        $this->gatewayResponseObject = $gatewayResponseObject;

        return $this;
    }

    public function getStatusMessage(): ?string
    {
        return $this->statusMessage;
    }

    public function setStatusMessage(string $statusMessage): static
    {
        $this->statusMessage = $statusMessage;

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

    public function getTransactionFee(): ?string
    {
        return $this->transactionFee;
    }

    public function setTransactionFee(string $transactionFee): static
    {
        $this->transactionFee = $transactionFee;

        return $this;
    }
}
