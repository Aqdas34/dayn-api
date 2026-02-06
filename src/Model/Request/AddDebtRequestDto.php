<?php

namespace App\Model\Request;

use Symfony\Component\Validator\Constraints as Assert;

class AddDebtRequestDto
{
    #[Assert\NotBlank(message: 'Phone Number is required!')]
    private string $phoneNumber;

    #[Assert\NotBlank(message: 'Amount is required!')]
    #[Assert\Positive(message: 'Amount must be greater than 0!')]
    #[Assert\Type(type: 'numeric', message: 'Amount must be a number')] // 💡 Accepts both int and float
    private float $amount;

    #[Assert\NotBlank(message: 'Description is required!')]
    private string $description;

    #[Assert\NotBlank(message: 'Type is required!')]
    private string $type;

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): AddDebtRequestDto
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): AddDebtRequestDto
    {
        $this->amount = $amount;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): AddDebtRequestDto
    {
        $this->description = $description;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): AddDebtRequestDto
    {
        $this->type = $type;
        return $this;
    }
}
