<?php

namespace App\Model\DTO;

use App\Entity\DebtCollection;
use App\Enum\DaynPartyType;
use App\Enum\DebtCollectionConfirmationStatus;
use Symfony\Component\Serializer\Attribute\SerializedName;

class DebtCollectionDto
{
    private string $uid;
    private string $amount;
    #[SerializedName('amount_unpaid')]
    private string $amountUnpaid;
    private string $description;
    #[SerializedName('phone_number')]
    private string $phoneNumber;
    private string $status;

    #[SerializedName('confirmation_status')]
    private DebtCollectionConfirmationStatus $confirmationStatus;

    #[SerializedName('confirmation_status_message')]
    private ?string $confirmationStatusMessage = null;
    private string $type;
    #[SerializedName('created_at')]
    private \DateTimeImmutable $createdAt;
    #[SerializedName('creditor_user_uid')]
    private string $creditorUserUid;
    #[SerializedName('debtor_user_uid')]
    private string $debtorUserUid;
    #[SerializedName('creditor_name')]
    private string $creditorName;
    #[SerializedName('debtor_name')]
    private string $debtorName;
    #[SerializedName('created_by_uid')]
    private string $createdByUid;

    public function getUid(): string
    {
        return $this->uid;
    }

    public function setUid(string $uid): DebtCollectionDto
    {
        $this->uid = $uid;
        return $this;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): DebtCollectionDto
    {
        $this->amount = $amount;
        return $this;
    }

    public function getAmountUnpaid(): string
    {
        return $this->amountUnpaid;
    }

    public function setAmountUnpaid(string $amountUnpaid): DebtCollectionDto
    {
        $this->amountUnpaid = $amountUnpaid;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): DebtCollectionDto
    {
        $this->description = $description;
        return $this;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): DebtCollectionDto
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): DebtCollectionDto
    {
        $this->status = $status;
        return $this;
    }

    public function getConfirmationStatus(): DebtCollectionConfirmationStatus
    {
        return $this->confirmationStatus;
    }

    public function setConfirmationStatus(DebtCollectionConfirmationStatus $confirmationStatus): DebtCollectionDto
    {
        $this->confirmationStatus = $confirmationStatus;
        return $this;
    }

    public function getConfirmationStatusMessage(): ?string
    {
        return $this->confirmationStatusMessage;
    }

    public function setConfirmationStatusMessage(?string $confirmationStatusMessage): DebtCollectionDto
    {
        $this->confirmationStatusMessage = $confirmationStatusMessage;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): DebtCollectionDto
    {
        $this->type = $type;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): DebtCollectionDto
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCreditorUserUid(): string
    {
        return $this->creditorUserUid;
    }

    public function setCreditorUserUid(string $creditorUserUid): DebtCollectionDto
    {
        $this->creditorUserUid = $creditorUserUid;
        return $this;
    }

    public function getDebtorUserUid(): string
    {
        return $this->debtorUserUid;
    }

    public function setDebtorUserUid(string $debtorUserUid): DebtCollectionDto
    {
        $this->debtorUserUid = $debtorUserUid;
        return $this;
    }

    public function getCreditorName(): string
    {
        return $this->creditorName;
    }

    public function setCreditorName(string $creditorName): DebtCollectionDto
    {
        $this->creditorName = $creditorName;
        return $this;
    }

    public function getDebtorName(): string
    {
        return $this->debtorName;
    }

    public function setDebtorName(string $debtorName): DebtCollectionDto
    {
        $this->debtorName = $debtorName;
        return $this;
    }

    public function getCreatedByUid(): string
    {
        return $this->createdByUid;
    }

    public function setCreatedByUid(string $createdByUid): DebtCollectionDto
    {
        $this->createdByUid = $createdByUid;
        return $this;
    }

    public static function fromDebtCollection(DebtCollection $debtCollection, bool $isDebtor): self
    {
        $type = $isDebtor ? DaynPartyType::DEBTOR : DaynPartyType::CREDITOR;
        $phoneNumber = $isDebtor ? $debtCollection->getCreditor()->getPhoneNumber() : $debtCollection->getDebtor()->getPhoneNumber();
        $debtor = $debtCollection->getDebtor();
        $creditor = $debtCollection->getCreditor();
        return (new self())
            ->setUid($debtCollection->getUid())
            ->setPhoneNumber($phoneNumber)
            ->setDescription($debtCollection->getDescription())
            ->setAmount($debtCollection->getAmount())
            ->setAmountUnpaid($debtCollection->getAmountUnpaid())
            ->setStatus($debtCollection->getStatus())
            ->setConfirmationStatus($debtCollection->getConfirmationStatus())
            ->setConfirmationStatusMessage($debtCollection->getConfirmationStatusMessage())
            ->setType($type->value)
            ->setCreatedAt($debtCollection->getCreatedAt())
            ->setCreditorUserUid($creditor->getUid())
            ->setCreditorName($creditor->getFullName())
            ->setDebtorUserUid($debtor->getUid())
            ->setDebtorName($debtor->getFullName())
            ->setCreatedByUid($debtCollection->getCreatedBy()->getUid());
    }
}