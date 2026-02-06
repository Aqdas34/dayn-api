<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_CLIENT_USERNAME', fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?UserWallet $wallet = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(length: 50)]
    private ?string $uid = null;

    /**
     * @var Collection<int, UserBankAccount>
     */
    #[ORM\OneToMany(targetEntity: UserBankAccount::class, mappedBy: 'user')]
    private Collection $bankAccounts;

    #[ORM\Column(length: 30)]
    private ?string $phoneNumber = null;

    /**
     * @var Collection<int, DebtCollection>
     */
    #[ORM\OneToMany(targetEntity: DebtCollection::class, mappedBy: 'debtor')]
    private Collection $debtCollections;

    /**
     * @var Collection<int, DebtCollection>
     */
    #[ORM\OneToMany(targetEntity: DebtCollection::class, mappedBy: 'creditor')]
    private Collection $creditCollections;

    /**
     * @var Collection<int, WitnessBinding>
     */
    #[ORM\OneToMany(targetEntity: WitnessBinding::class, mappedBy: 'user')]
    private Collection $witnessBindings;

    /**
     * @var Collection<int, UserInvitation>
     */
    #[ORM\OneToMany(targetEntity: UserInvitation::class, mappedBy: 'user')]
    private Collection $userInvitations;

    /**
     * @var Collection<int, Beneficiary>
     */
    #[ORM\OneToMany(targetEntity: Beneficiary::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private Collection $beneficiaries;

    public function __construct()
    {
        $this->bankAccounts = new ArrayCollection();
        $this->debtCollections = new ArrayCollection();
        $this->creditCollections = new ArrayCollection();
        $this->witnessBindings = new ArrayCollection();
        $this->userInvitations = new ArrayCollection();
        $this->beneficiaries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        $roles[] = 'ROLE_CLIENT';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getWallet(): ?UserWallet
    {
        return $this->wallet;
    }

    public function setWallet(UserWallet $wallet): static
    {
        // set the owning side of the relation if necessary
        if ($wallet->getUser() !== $this) {
            $wallet->setUser($this);
        }

        $this->wallet = $wallet;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

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

    /**
     * @return Collection<int, UserBankAccount>
     */
    public function getBankAccounts(): Collection
    {
        return $this->bankAccounts;
    }

    public function addBankAccount(UserBankAccount $bankAccount): static
    {
        if (!$this->bankAccounts->contains($bankAccount)) {
            $this->bankAccounts->add($bankAccount);
            $bankAccount->setUser($this);
        }

        return $this;
    }

    public function removeBankAccount(UserBankAccount $bankAccount): static
    {
        if ($this->bankAccounts->removeElement($bankAccount)) {
            // set the owning side to null (unless already changed)
            if ($bankAccount->getUser() === $this) {
                $bankAccount->setUser(null);
            }
        }

        return $this;
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

    /**
     * @return Collection<int, DebtCollection>
     */
    public function getDebtCollections(): Collection
    {
        return $this->debtCollections;
    }

    public function addDebtCollection(DebtCollection $debtCollection): static
    {
        if (!$this->debtCollections->contains($debtCollection)) {
            $this->debtCollections->add($debtCollection);
            $debtCollection->setDebtor($this);
        }

        return $this;
    }

    public function removeDebtCollection(DebtCollection $debtCollection): static
    {
        if ($this->debtCollections->removeElement($debtCollection)) {
            // set the owning side to null (unless already changed)
            if ($debtCollection->getDebtor() === $this) {
                $debtCollection->setDebtor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DebtCollection>
     */
    public function getCreditCollections(): Collection
    {
        return $this->creditCollections;
    }

    public function addCreditCollection(DebtCollection $creditCollection): static
    {
        if (!$this->creditCollections->contains($creditCollection)) {
            $this->creditCollections->add($creditCollection);
            $creditCollection->setCreditor($this);
        }

        return $this;
    }

    public function removeCreditorCollection(DebtCollection $creditorCollection): static
    {
        if ($this->creditCollections->removeElement($creditorCollection)) {
            // set the owning side to null (unless already changed)
            if ($creditorCollection->getCreditor() === $this) {
                $creditorCollection->setCreditor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, WitnessBinding>
     */
    public function getWitnessBindings(): Collection
    {
        return $this->witnessBindings;
    }

    public function addWitnessBinding(WitnessBinding $witnessBinding): static
    {
        if (!$this->witnessBindings->contains($witnessBinding)) {
            $this->witnessBindings->add($witnessBinding);
            $witnessBinding->setUser($this);
        }

        return $this;
    }

    public function removeWitnessBinding(WitnessBinding $witnessBinding): static
    {
        if ($this->witnessBindings->removeElement($witnessBinding)) {
            // set the owning side to null (unless already changed)
            if ($witnessBinding->getUser() === $this) {
                $witnessBinding->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserInvitation>
     */
    public function getUserInvitations(): Collection
    {
        return $this->userInvitations;
    }

    public function addUserInvitation(UserInvitation $userInvitation): static
    {
        if (!$this->userInvitations->contains($userInvitation)) {
            $this->userInvitations->add($userInvitation);
            $userInvitation->setUser($this);
        }

        return $this;
    }

    public function removeUserInvitation(UserInvitation $userInvitation): static
    {
        if ($this->userInvitations->removeElement($userInvitation)) {
            // set the owning side to null (unless already changed)
            if ($userInvitation->getUser() === $this) {
                $userInvitation->setUser(null);
            }
        }

        return $this;
    }

    public function getFullName(): string
    {
        return "$this->firstName $this->lastName";
    }

    /**
     * @return Collection<int, Beneficiary>
     */
    public function getBeneficiaries(): Collection
    {
        return $this->beneficiaries;
    }

    public function addBeneficiary(Beneficiary $beneficiary): static
    {
        if (!$this->beneficiaries->contains($beneficiary)) {
            $this->beneficiaries->add($beneficiary);
            $beneficiary->setUser($this);
        }

        return $this;
    }

    public function removeBeneficiary(Beneficiary $beneficiary): static
    {
        if ($this->beneficiaries->removeElement($beneficiary)) {
            // set the owning side to null (unless already changed)
            if ($beneficiary->getUser() === $this) {
                $beneficiary->setUser(null);
            }
        }

        return $this;
    }
}
