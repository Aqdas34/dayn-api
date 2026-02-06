<?php

namespace App\Model\DTO;

use App\Entity\User;
use App\Entity\WitnessBinding;
use Symfony\Component\Serializer\Attribute\SerializedName;

class UserWitnessBindingDto
{
    private int $witnessBindingId;
    #[SerializedName('full_name')]
    private string $fullName;
    private string $email;
    #[SerializedName('phone_number')]
    private string $phoneNumber;

    public function getWitnessBindingId(): int
    {
        return $this->witnessBindingId;
    }

    public function setWitnessBindingId(int $witnessBindingId): UserWitnessBindingDto
    {
        $this->witnessBindingId = $witnessBindingId;
        return $this;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): UserWitnessBindingDto
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): UserWitnessBindingDto
    {
        $this->email = $email;
        return $this;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): UserWitnessBindingDto
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public static function fromWitnessBinding(WitnessBinding $witnessBinding): self
    {
        $user = $witnessBinding->getWitness();
        return (new self())
            ->setWitnessBindingId($witnessBinding->getId())
            ->setFullName($user->getFullName())
            ->setEmail($user->getUserIdentifier())
            ->setPhoneNumber($user->getPhoneNumber());
    }
}