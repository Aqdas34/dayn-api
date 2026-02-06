<?php

namespace App\Model\Request;

use Symfony\Component\Serializer\Attribute\SerializedName;

class AddWitnessRequestDto
{
    #[SerializedName('phone_number')]
    private string $phoneNumber;

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): AddWitnessRequestDto
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }
}