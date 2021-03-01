<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AccountRepository::class)
 *  @ApiResource(
 *     routePrefix="/admin",
 *     denormalizationContext={"groups"={"compte:write"}},
 *     normalizationContext={"groups"={"compte:read"}},
 *     attributes={
 *   "security"="is_granted('ROLE_AdminSystem')",
 *   "security_message"="Ressource accessible que par l'Admin",
 * },
 *     collectionOperations={
 *     "get",
 *      "post"
 *     },
 *      itemOperations={
 *     "get",
 *     "put",
 *     "delete"
 *     }
 * )
 */
class Account
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ({"compte:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Groups ({"compte:write","compte:read"})
     */
    private $accountNumber;

    /**
     * @ORM\Column(type="integer")
     *  /**
     * @Assert\GreaterThanOrEqual(
     *     value = 700000,
     *     message="initialiser le compte à 700000 ou plus."
     * )
     * @Groups ({"compte:write","compte:read"})
     *
     */
    private $balance;

    /**
     * @ORM\OneToMany(targetEntity=Deposit::class, mappedBy="account")
     */
    private $depot;

    /**
     * @ORM\OneToOne(targetEntity=Agency::class, mappedBy="account", cascade={"persist", "remove"})
     */
    private $agency;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isArchived=false;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    public function __construct()
    {
        $this->depot = new ArrayCollection();

        $this->date= (new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(string $accountNumber): self
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    public function getBalance(): ?int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @return Collection|Deposit[]
     */
    public function getDepot(): Collection
    {
        return $this->depot;
    }

    public function addDepot(Deposit $depot): self
    {
        if (!$this->depot->contains($depot)) {
            $this->depot[] = $depot;
            $depot->setAccount($this);
        }

        return $this;
    }

    public function removeDepot(Deposit $depot): self
    {
        if ($this->depot->removeElement($depot)) {
            // set the owning side to null (unless already changed)
            if ($depot->getAccount() === $this) {
                $depot->setAccount(null);
            }
        }

        return $this;
    }

    public function getAgency(): ?Agency
    {
        return $this->agency;
    }

    public function setAgency(Agency $agency): self
    {
        // set the owning side of the relation if necessary
        if ($agency->getAccount() !== $this) {
            $agency->setAccount($this);
        }

        $this->agency = $agency;

        return $this;
    }

    public function getIsArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): self
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}