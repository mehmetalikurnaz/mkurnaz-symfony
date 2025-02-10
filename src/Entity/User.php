<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\Email()]
    #[Assert\Length(min: 4, max: 175)]
    private string $email;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column]
    #[Assert\NotBlank]
    private string $password;

    #[ORM\OneToMany(targetEntity: Invoice::class, mappedBy: 'user', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $invoices;

    #[ORM\OneToMany(targetEntity: CompanyData::class, mappedBy: 'user', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $companyData;

    public function __construct(string $email)
    {
        $this->email = $email;
        $this->roles = ['ROLE_USER']; // Varsayılan rol
        $this->invoices = new ArrayCollection();
        $this->companyData = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = array_unique($roles);
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Geçici hassas verileri temizlemek için kullanılabilir
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'roles' => $this->roles,
        ];
    }

    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): static
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices->add($invoice);
            $invoice->setUser($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): static
    {
        if ($this->invoices->removeElement($invoice) && $invoice->getUser() === $this) {
            $invoice->setUser(null);
        }

        return $this;
    }

    public function getCompanyData(): Collection
    {
        return $this->companyData;
    }

    public function addCompanyData(CompanyData $companyData): static
    {
        if (!$this->companyData->contains($companyData)) {
            $this->companyData->add($companyData);
            $companyData->setUser($this);
        }

        return $this;
    }

    public function removeCompanyData(CompanyData $companyData): static
    {
        if ($this->companyData->removeElement($companyData) && $companyData->getUser() === $this) {
            $companyData->setUser(null);
        }

        return $this;
    }
}
