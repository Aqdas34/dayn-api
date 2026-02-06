<?php

namespace App\Entity;

use App\Enum\DebtCollectionConfirmationStatus;
use App\Repository\DebtCollectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DebtCollectionRepository::class)]
class DebtCollection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $phoneNumber = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $amount = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'debtCollections')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $debtor = null;

    #[ORM\ManyToOne(inversedBy: 'creditCollections')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $creditor = null;

    #[ORM\Column(length: 30)]
    private ?string $status = null;

    #[ORM\Column(length: 50)]
    private ?string $uid = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastModifiedAt = null;

    #[ORM\Column]
    private ?int $amountUnpaid = null;

    /**
     * @var Collection<int, DebtCollectionPayment>
     */
    #[ORM\OneToMany(targetEntity: DebtCollectionPayment::class, mappedBy: 'debtCollection')]
    private Collection $payments;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\Column(enumType: DebtCollectionConfirmationStatus::class)]
    private ?DebtCollectionConfirmationStatus $confirmationStatus = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $confirmationStatusMessage = null;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDebtor(): ?User
    {
        return $this->debtor;
    }

    public function setDebtor(?User $debtor): static
    {
        $this->debtor = $debtor;

        return $this;
    }

    public function getCreditor(): ?User
    {
        return $this->creditor;
    }

    public function setCreditor(?User $creditor): static
    {
        $this->creditor = $creditor;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(string $uid): static
    {
        $this->uid = $uid;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getLastModifiedAt(): ?\DateTimeImmutable
    {
        return $this->lastModifiedAt;
    }

    public function setLastModifiedAt(\DateTimeImmutable $lastModifiedAt): static
    {
        $this->lastModifiedAt = $lastModifiedAt;

        return $this;
    }

    public function getAmountUnpaid(): ?int
    {
        return $this->amountUnpaid;
    }

    public function setAmountUnpaid(int $amountUnpaid): static
    {
        $this->amountUnpaid = $amountUnpaid;

        return $this;
    }

    /**
     * @return Collection<int, DebtCollectionPayment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(DebtCollectionPayment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setDebtCollection($this);
        }

        return $this;
    }

    public function removePayment(DebtCollectionPayment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getDebtCollection() === $this) {
                $payment->setDebtCollection(null);
            }
        }

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getConfirmationStatus(): ?DebtCollectionConfirmationStatus
    {
        return $this->confirmationStatus;
    }

    public function setConfirmationStatus(DebtCollectionConfirmationStatus $confirmationStatus): static
    {
        $this->confirmationStatus = $confirmationStatus;

        return $this;
    }

    public function getConfirmationStatusMessage(): ?string
    {
        return $this->confirmationStatusMessage;
    }

    public function setConfirmationStatusMessage(?string $confirmationStatusMessage): static
    {
        $this->confirmationStatusMessage = $confirmationStatusMessage;

        return $this;
    }
}
