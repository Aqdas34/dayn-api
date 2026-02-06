<?php

namespace App\Entity;

use App\Repository\WitnessBindingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WitnessBindingRepository::class)]
class WitnessBinding
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'witnessBindings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $witness = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getWitness(): ?User
    {
        return $this->witness;
    }

    public function setWitness(?User $witness): static
    {
        $this->witness = $witness;

        return $this;
    }
}
