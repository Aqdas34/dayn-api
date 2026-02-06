<?php

namespace App\Model\Request;

use Symfony\Component\Validator\Constraints as Assert;

class AddBeneficiaryRequestDto
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private string $displayName;

    #[Assert\NotBlank]
    #[Assert\PhoneNumber]
    private string $phoneNumber;

    #[Assert\Length(max: 50)]
    private ?string $category = null;

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
}
