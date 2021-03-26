<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\AccountRepository;
use DateTime;
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
 *
 *     collectionOperations={
 *     "get"={
 *      "access_control"="(is_granted('ROLE_AdminAgence') or is_granted('ROLE_UserAgence')  or is_granted('ROLE_AdminSystem'))",
 *     },
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
     * @Groups ({"user:read","compte:write","compte:read","agence:read"})
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
     * @ApiSubresource()
     */
    private $depot;

    /**
     * @ORM\OneToOne(targetEntity=Agency::class, mappedBy="account", cascade={"persist", "remove"})
     * @Groups ({"compte:read"})
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

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="account_depot")
     */
    private $transaction_depot;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="account_retrait")
     */
    private $transaction_retrait;



    public function __construct()
    {
        $this->depot = new ArrayCollection();

        $this->date= (new DateTime());
        $this->transaction_depot = new ArrayCollection();
        $this->transaction_retrait = new ArrayCollection();

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

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactionDepot(): Collection
    {
        return $this->transaction_depot;
    }

    public function addTransactionDepot(Transaction $transactionDepot): self
    {
        if (!$this->transaction_depot->contains($transactionDepot)) {
            $this->transaction_depot[] = $transactionDepot;
            $transactionDepot->setAccountDepot($this);
        }

        return $this;
    }

    public function removeTransactionDepot(Transaction $transactionDepot): self
    {
        if ($this->transaction_depot->removeElement($transactionDepot)) {
            // set the owning side to null (unless already changed)
            if ($transactionDepot->getAccountDepot() === $this) {
                $transactionDepot->setAccountDepot(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactionRetrait(): Collection
    {
        return $this->transaction_retrait;
    }

    public function addTransactionRetrait(Transaction $transactionRetrait): self
    {
        if (!$this->transaction_retrait->contains($transactionRetrait)) {
            $this->transaction_retrait[] = $transactionRetrait;
            $transactionRetrait->setAccountRetrait($this);
        }

        return $this;
    }

    public function removeTransactionRetrait(Transaction $transactionRetrait): self
    {
        if ($this->transaction_retrait->removeElement($transactionRetrait)) {
            // set the owning side to null (unless already changed)
            if ($transactionRetrait->getAccountRetrait() === $this) {
                $transactionRetrait->setAccountRetrait(null);
            }
        }

        return $this;
    }
}
