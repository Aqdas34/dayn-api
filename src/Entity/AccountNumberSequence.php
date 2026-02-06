<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class AccountNumberSequence
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 3)]
    private string $prefix;

    #[ORM\Column(type: 'integer')]
    private int $lastAccountNumber;

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function getLastAccountNumber(): int
    {
        return $this->lastAccountNumber;
    }

    public function setLastAccountNumber(int $lastAccountNumber): self
    {
        $this->lastAccountNumber = $lastAccountNumber;
        return $this;
    }
}
