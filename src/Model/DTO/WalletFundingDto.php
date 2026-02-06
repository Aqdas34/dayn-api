<?php

namespace App\Model\DTO;

use App\Entity\PaymentTransactionalAccount;
use App\Entity\WalletFunding;
use App\Model\Integration\Common\PaymentTransactionChannel;
use Symfony\Component\Serializer\Attribute\SerializedName;

class WalletFundingDto
{
    private string $uid;
    private string $reference;
    private string $amount;

    #[SerializedName('transaction_fee')]
    private string $transactionFee;
    private string $narration;
    private string $status;

    #[SerializedName('status_message')]
    private string $statusMessage;

    #[SerializedName('created_at')]
    private \DateTimeImmutable $createdAt;

    #[SerializedName('last_modified_at')]
    private \DateTimeImmutable $lastModifiedAt;

    public function getUid(): string
    {
        return $this->uid;
    }

    public function setUid(string $uid): WalletFundingDto
    {
        $this->uid = $uid;
        return $this;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference): WalletFundingDto
    {
        $this->reference = $reference;
        return $this;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): WalletFundingDto
    {
        $this->amount = $amount;
        return $this;
    }

    public function getTransactionFee(): string
    {
        return $this->transactionFee;
    }

    public function setTransactionFee(string $transactionFee): WalletFundingDto
    {
        $this->transactionFee = $transactionFee;
        return $this;
    }

    public function getNarration(): string
    {
        return $this->narration;
    }

    public function setNarration(string $narration): WalletFundingDto
    {
        $this->narration = $narration;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): WalletFundingDto
    {
        $this->status = $status;
        return $this;
    }

    public function getStatusMessage(): string
    {
        return $this->statusMessage;
    }

    public function setStatusMessage(string $statusMessage): WalletFundingDto
    {
        $this->statusMessage = $statusMessage;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): WalletFundingDto
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getLastModifiedAt(): \DateTimeImmutable
    {
        return $this->lastModifiedAt;
    }

    public function setLastModifiedAt(\DateTimeImmutable $lastModifiedAt): WalletFundingDto
    {
        $this->lastModifiedAt = $lastModifiedAt;
        return $this;
    }

    public static function fromWalletFunding(WalletFunding $walletFunding, $payload = null): self
    {
        if ($payload !== null) {
            if ($walletFunding->getTransactionChannel() === PaymentTransactionChannel::BANK_TRANSFER) {
                assert($payload instanceof PaymentTransactionalAccount);
            }
        }
        return (new self())
            ->setUid($walletFunding->getUid())
            ->setReference($walletFunding->getReference())
            ->setAmount($walletFunding->getAmount())
            ->setTransactionFee($walletFunding->getTransactionFee())
            ->setNarration($walletFunding->getNarration())
            ->setStatus($walletFunding->getStatus()->value)
            ->setStatusMessage($walletFunding->getStatusMessage())
            ->setCreatedAt($walletFunding->getCreatedAt())
            ->setLastModifiedAt($walletFunding->getLastModifiedAt());
    }
}