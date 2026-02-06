<?php

namespace App\Entity;

use App\Repository\UserWalletRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserWalletRepository::class)]
class UserWallet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $balance = null;

    #[ORM\OneToOne(inversedBy: 'wallet', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, WalletPayout>
     */
    #[ORM\OneToMany(targetEntity: WalletPayout::class, mappedBy: 'wallet')]
    private Collection $walletPayouts;

    /**
     * @var Collection<int, WalletFunding>
     */
    #[ORM\OneToMany(targetEntity: WalletFunding::class, mappedBy: 'wallet')]
    private Collection $walletFundings;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $transactionPin = null;

    public function __construct()
    {
        $this->walletPayouts = new ArrayCollection();
        $this->walletFundings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBalance(): ?string
    {
        return $this->balance;
    }

    public function setBalance(string $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, WalletPayout>
     */
    public function getWalletPayouts(): Collection
    {
        return $this->walletPayouts;
    }

    public function addWalletPayout(WalletPayout $walletPayout): static
    {
        if (!$this->walletPayouts->contains($walletPayout)) {
            $this->walletPayouts->add($walletPayout);
            $walletPayout->setWallet($this);
        }

        return $this;
    }

    public function removeWalletPayout(WalletPayout $walletPayout): static
    {
        if ($this->walletPayouts->removeElement($walletPayout)) {
            // set the owning side to null (unless already changed)
            if ($walletPayout->getWallet() === $this) {
                $walletPayout->setWallet(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, WalletFunding>
     */
    public function getWalletFundings(): Collection
    {
        return $this->walletFundings;
    }

    public function addWalletFunding(WalletFunding $walletFunding): static
    {
        if (!$this->walletFundings->contains($walletFunding)) {
            $this->walletFundings->add($walletFunding);
            $walletFunding->setWallet($this);
        }

        return $this;
    }

    public function removeWalletFunding(WalletFunding $walletFunding): static
    {
        if ($this->walletFundings->removeElement($walletFunding)) {
            // set the owning side to null (unless already changed)
            if ($walletFunding->getWallet() === $this) {
                $walletFunding->setWallet(null);
            }
        }

        return $this;
    }

    public function getTransactionPin(): ?string
    {
        return $this->transactionPin;
    }

    public function setTransactionPin(?string $transactionPin): static
    {
        $this->transactionPin = $transactionPin;

        return $this;
    }
}
