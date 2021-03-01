<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 *  @ApiResource(
 *     routePrefix="/admin",
 *     normalizationContext={"groups"={"trans:read"}},
 *     attributes={
 *  "denormalization_context"={"groups"={"t:write"}},
 * },
 *     collectionOperations={
 *     "depot"={
 *          "method"="POST",
 *          "route_name"="depot"
 *     }
 *     },
 *      itemOperations={
 *     "get",
 *     "retrait"={
 *          "method"="PUT",
 *          "route_name"="retrait"
 *     },
 *     }
 * )
 */
class Client
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"trans:write"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Groups({"t:write"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="integer")
     *  @Groups({"t:write"})
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     *  @Groups({"t:write"})
     */
    private $cni;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="clientDepot")
     */
    private $transactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(int $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCni(): ?string
    {
        return $this->cni;
    }

    public function setCni(string $cni): self
    {
        $this->cni = $cni;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setClientDepot($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getClientDepot() === $this) {
                $transaction->setClientDepot(null);
            }
        }

        return $this;
    }

}
