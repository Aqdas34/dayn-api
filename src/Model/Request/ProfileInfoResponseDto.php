<?php

namespace App\Model\Request;

use Symfony\Component\Serializer\Attribute\SerializedName;

class ProfileInfoResponseDto
{
    private string $uid;
    #[SerializedName('first_name')]
    private string $firstName;
    #[SerializedName('last_name')]
    private string $lastName;
    private string $email;
    #[SerializedName('phone_number')]
    private string $phoneNumber;

    public function getUid(): string
    {
        return $this->uid;
    }

    public function setUid(string $uid): ProfileInfoResponseDto
    {
        $this->uid = $uid;
        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): ProfileInfoResponseDto
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): ProfileInfoResponseDto
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): ProfileInfoResponseDto
    {
        $this->email = $email;
        return $this;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): ProfileInfoResponseDto
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }
}