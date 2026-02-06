<?php

namespace App\Model\DTO;

use App\Entity\WalletPayout;

class WalletPayoutDto
{
    private string $uid;
    private string $reference;
    private int $amount;
    private int $transactionFee;
    private string $narration;
    private string $status;
    private string $statusMessage;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $lastModifiedAt;

    public function getUid(): string
    {
        return $this->uid;
    }

    public function setUid(string $uid): WalletPayoutDto
    {
        $this->uid = $uid;
        return $this;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference): WalletPayoutDto
    {
        $this->reference = $reference;
        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): WalletPayoutDto
    {
        $this->amount = $amount;
        return $this;
    }

    public function getTransactionFee(): int
    {
        return $this->transactionFee;
    }

    public function setTransactionFee(int $transactionFee): WalletPayoutDto
    {
        $this->transactionFee = $transactionFee;
        return $this;
    }

    public function getNarration(): string
    {
        return $this->narration;
    }

    public function setNarration(string $narration): WalletPayoutDto
    {
        $this->narration = $narration;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): WalletPayoutDto
    {
        $this->status = $status;
        return $this;
    }

    public function getStatusMessage(): string
    {
        return $this->statusMessage;
    }

    public function setStatusMessage(string $statusMessage): WalletPayoutDto
    {
        $this->statusMessage = $statusMessage;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): WalletPayoutDto
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getLastModifiedAt(): \DateTimeImmutable
    {
        return $this->lastModifiedAt;
    }

    public function setLastModifiedAt(\DateTimeImmutable $lastModifiedAt): WalletPayoutDto
    {
        $this->lastModifiedAt = $lastModifiedAt;
        return $this;
    }

    public static function fromWalletPayout(WalletPayout $walletPayout): self
    {
        return (new self())
            ->setUid($walletPayout->getUid())
            ->setReference($walletPayout->getReference())
            ->setAmount($walletPayout->getAmount())
            ->setTransactionFee($walletPayout->getTransactionFee())
            ->setNarration($walletPayout->getNarration())
            ->setStatus($walletPayout->getStatus()->value)
            ->setStatusMessage($walletPayout->getStatusMessage())
            ->setCreatedAt($walletPayout->getCreatedAt())
            ->setLastModifiedAt($walletPayout->getLastModifiedAt());
    }
}