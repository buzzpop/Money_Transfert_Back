<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\AgencyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AgencyRepository::class)
 *  * @ApiResource(
 *     routePrefix="/admin",
 *     normalizationContext={"groups"={"agence:read"}},
 *     attributes={
 *   "security"="is_granted('ROLE_AdminSystem')",
 *   "security_message"="Ressource accessible que par l'Admin",
 *  "denormalization_context"={"groups"={"agence:write"}},
 * },
 *     collectionOperations={
 *     "get",
 *     "post",
 *     },
 *      itemOperations={
 *     "get",
 *     "put",
 *     "delete"={
 *      "path"="/agencies/{id}/users/{idU}"
 *     }
 *     }
 * )
 */
class Agency
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"agence:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"agence:read","agence:write"})
     */
    private $agencyName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"agence:read","agence:write"})
     */
    private $agencyAddress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"agence:read","agence:write"})
     */
    private $status;



    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="agency")
     * @ApiSubresource()
     */
    private $users;

    /**
     * @ORM\OneToOne(targetEntity=Account::class, inversedBy="agency", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @ApiSubresource()
     *
     */
    private $account;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isArchived=false;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAgencyName(): ?string
    {
        return $this->agencyName;
    }

    public function setAgencyName(string $agencyName): self
    {
        $this->agencyName = $agencyName;

        return $this;
    }

    public function getAgencyAddress(): ?string
    {
        return $this->agencyAddress;
    }

    public function setAgencyAddress(string $agencyAddress): self
    {
        $this->agencyAddress = $agencyAddress;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }


    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setAgency($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getAgency() === $this) {
                $user->setAgency(null);
            }
        }

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): self
    {
        $this->account = $account;

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
}
