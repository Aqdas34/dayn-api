<?php

namespace App\Model\DTO;

use App\Entity\Beneficiary;
use Symfony\Component\Serializer\Attribute\SerializedName;

class BeneficiaryDto
{
    private string $uid;
    #[SerializedName('display_name')]
    private string $displayName;
    #[SerializedName('phone_number')]
    private string $phoneNumber;
    private ?string $category = null;
    #[SerializedName('registration_status')]
    private bool $registrationStatus;
    #[SerializedName('created_at')]
    private \DateTimeImmutable $createdAt;

    public function getUid(): string
    {
        return $this->uid;
    }

    public function setUid(string $uid): self
    {
        $this->uid = $uid;
        return $this;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): self
    {
        $this->displayName = $displayName;
        return $this;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function isRegistrationStatus(): bool
    {
        return $this->registrationStatus;
    }

    public function setRegistrationStatus(bool $registrationStatus): self
    {
        $this->registrationStatus = $registrationStatus;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public static function fromBeneficiary(Beneficiary $beneficiary, bool $isRegistered): self
    {
        return (new self())
            ->setUid($beneficiary->getUid())
            ->setDisplayName($beneficiary->getDisplayName())
            ->setPhoneNumber($beneficiary->getPhoneNumber())
            ->setCategory($beneficiary->getCategory())
            ->setRegistrationStatus($isRegistered)
            ->setCreatedAt($beneficiary->getCreatedAt());
    }
}
