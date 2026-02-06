<?php

namespace App\Model\DTO;

use App\Entity\DebtCollectionPayment;
use Symfony\Component\Serializer\Attribute\SerializedName;

class DebtCollectionPaymentDto
{
    private string $uid;
    private string $amount;
    private string $channel;
    private string $status;
    #[SerializedName('payment_reference')]
    private ?string $paymentReference;
    #[SerializedName('created_at')]
    private \DateTimeImmutable $createdAt;
    #[SerializedName('last_modified_at')]
    private \DateTimeImmutable $lastModifiedAt;
    #[SerializedName('debt_collection_uid')]
    private string $debtCollectionUid;
    #[SerializedName('debtor_user_uid')]
    private string $debtorUserUid;
    #[SerializedName('debtor_name')]
    private string $debtorName;
    #[SerializedName('debtor_phone_number')]
    private string $debtorPhoneNumber;
    #[SerializedName('creditor_user_uid')]
    private string $creditorUserUid;
    #[SerializedName('creditor_name')]
    private string $creditorName;
    #[SerializedName('creditor_phone_number')]
    private string $creditorPhoneNumber;
    #[SerializedName('is_acknowledged')]
    private bool $isAcknowledged;

    public function getUid(): string
    {
        return $this->uid;
    }

    public function setUid(string $uid): DebtCollectionPaymentDto
    {
        $this->uid = $uid;
        return $this;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): DebtCollectionPaymentDto
    {
        $this->amount = $amount;
        return $this;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }

    public function setChannel(string $channel): DebtCollectionPaymentDto
    {
        $this->channel = $channel;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): DebtCollectionPaymentDto
    {
        $this->status = $status;
        return $this;
    }

    public function getPaymentReference(): ?string
    {
        return $this->paymentReference;
    }

    public function setPaymentReference(?string $paymentReference): DebtCollectionPaymentDto
    {
        $this->paymentReference = $paymentReference;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): DebtCollectionPaymentDto
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getLastModifiedAt(): \DateTimeImmutable
    {
        return $this->lastModifiedAt;
    }

    public function setLastModifiedAt(\DateTimeImmutable $lastModifiedAt): DebtCollectionPaymentDto
    {
        $this->lastModifiedAt = $lastModifiedAt;
        return $this;
    }

    public function getDebtCollectionUid(): string
    {
        return $this->debtCollectionUid;
    }

    public function setDebtCollectionUid(string $debtCollectionUid): DebtCollectionPaymentDto
    {
        $this->debtCollectionUid = $debtCollectionUid;
        return $this;
    }

    public function getDebtorUserUid(): string
    {
        return $this->debtorUserUid;
    }

    public function setDebtorUserUid(string $debtorUserUid): DebtCollectionPaymentDto
    {
        $this->debtorUserUid = $debtorUserUid;
        return $this;
    }

    public function getDebtorName(): string
    {
        return $this->debtorName;
    }

    public function setDebtorName(string $debtorName): DebtCollectionPaymentDto
    {
        $this->debtorName = $debtorName;
        return $this;
    }

    public function getDebtorPhoneNumber(): string
    {
        return $this->debtorPhoneNumber;
    }

    public function setDebtorPhoneNumber(string $debtorPhoneNumber): DebtCollectionPaymentDto
    {
        $this->debtorPhoneNumber = $debtorPhoneNumber;
        return $this;
    }

    public function getCreditorUserUid(): string
    {
        return $this->creditorUserUid;
    }

    public function setCreditorUserUid(string $creditorUserUid): DebtCollectionPaymentDto
    {
        $this->creditorUserUid = $creditorUserUid;
        return $this;
    }

    public function getCreditorName(): string
    {
        return $this->creditorName;
    }

    public function setCreditorName(string $creditorName): DebtCollectionPaymentDto
    {
        $this->creditorName = $creditorName;
        return $this;
    }

    public function getCreditorPhoneNumber(): string
    {
        return $this->creditorPhoneNumber;
    }

    public function setCreditorPhoneNumber(string $creditorPhoneNumber): DebtCollectionPaymentDto
    {
        $this->creditorPhoneNumber = $creditorPhoneNumber;
        return $this;
    }

    public function isAcknowledged(): bool
    {
        return $this->isAcknowledged;
    }

    public function setIsAcknowledged(bool $isAcknowledged): DebtCollectionPaymentDto
    {
        $this->isAcknowledged = $isAcknowledged;
        return $this;
    }

    public static function fromDebtCollectionPayment(DebtCollectionPayment $collectionPayment): self
    {
        $debtCollection = $collectionPayment->getDebtCollection();
        $debtor = $debtCollection->getDebtor();
        $creditor = $debtCollection->getCreditor();
        return (new self())
            ->setUid($collectionPayment->getUid())
            ->setAmount($collectionPayment->getAmount())
            ->setChannel($collectionPayment->getChannel())
            ->setStatus($collectionPayment->getStatus())
            ->setPaymentReference($collectionPayment->getPaymentReference())
            ->setCreatedAt($collectionPayment->getCreatedAt())
            ->setLastModifiedAt($collectionPayment->getLastModifiedAt())
            ->setDebtCollectionUid($debtCollection->getUid())
            ->setDebtorUserUid($debtor->getUid())
            ->setDebtorName($debtor->getFullName())
            ->setDebtorPhoneNumber($debtor->getPhoneNumber())
            ->setCreditorUserUid($creditor->getUid())
            ->setCreditorName($creditor->getFullName())
            ->setCreditorPhoneNumber($creditor->getPhoneNumber())
            ->setIsAcknowledged(method_exists($collectionPayment, 'isAcknowledged') ? ($collectionPayment->isAcknowledged() ?? false) : false);
    }
}