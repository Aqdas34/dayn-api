<?php

namespace App\Entity;

use App\Repository\UserBankAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserBankAccountRepository::class)]
class UserBankAccount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $accountNumber = null;

    #[ORM\Column(length: 100)]
    private ?string $accountName = null;

    #[ORM\Column(length: 10)]
    private ?string $bankCode = null;

    #[ORM\Column(length: 10)]
    private ?string $currency = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $gatewayRecipientCode = null;

    #[ORM\ManyToOne(inversedBy: 'bankAccounts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $bankName = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $paystackSubaccountCode = null;


    /**
     * @var Collection<int, WalletPayout>
     */
    #[ORM\OneToMany(targetEntity: WalletPayout::class, mappedBy: 'receivingBankAccount')]
    private Collection $walletPayouts;

    public function __construct()
    {
        $this->walletPayouts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(string $accountNumber): static
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    public function getAccountName(): ?string
    {
        return $this->accountName;
    }

    public function setAccountName(string $accountName): static
    {
        $this->accountName = $accountName;

        return $this;
    }

    public function getBankCode(): ?string
    {
        return $this->bankCode;
    }

    public function setBankCode(string $bankCode): static
    {
        $this->bankCode = $bankCode;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getGatewayRecipientCode(): ?string
    {
        return $this->gatewayRecipientCode;
    }

    public function setGatewayRecipientCode(?string $gatewayRecipientCode): static
    {
        $this->gatewayRecipientCode = $gatewayRecipientCode;

        return $this;
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

    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    public function setBankName(string $bankName): static
    {
        $this->bankName = $bankName;

        return $this;
    }

    public function getPaystackSubaccountCode(): ?string
    {
        return $this->paystackSubaccountCode;
    }

    public function setPaystackSubaccountCode(?string $paystackSubaccountCode): static
    {
        $this->paystackSubaccountCode = $paystackSubaccountCode;

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
            $walletPayout->setReceivingBankAccount($this);
        }

        return $this;
    }

    public function removeWalletPayout(WalletPayout $walletPayout): static
    {
        if ($this->walletPayouts->removeElement($walletPayout)) {
            // set the owning side to null (unless already changed)
            if ($walletPayout->getReceivingBankAccount() === $this) {
                $walletPayout->setReceivingBankAccount(null);
            }
        }

        return $this;
    }
}
