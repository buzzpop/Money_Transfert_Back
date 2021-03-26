<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Annotation\ApiFilter;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 *@ApiFilter(BooleanFilter::class, properties={"isArchived"})
 * @ApiResource(
 *      routePrefix="/admin",
 *     normalizationContext={"groups"={"user:read"}},
 *     denormalizationContext={"groups"={"users:write"}},
 *     collectionOperations={
 *     "get"={
 *      "access_control"="(is_granted('ROLE_AdminSystem'))",
 *     },
 *     "add_user"={
 * "method"="POST",
 * "access_control"="(is_granted('ROLE_AdminSystem'))",
 * "route_name"="add_user",
 *      "deserialize"=false,
 *             "swagger_context"={
 *                 "consumes"={
 *                     "multipart/form-data",
 *                 },
 *                 "parameters"={
 *                     {
 *                         "in"="formData",
 *                         "name"="file",
 *                         "type"="file",
 *                         "description"="The file to upload",
 *                     },
 *                 },
 *             },
 *          }
 *     },
 *      itemOperations={
 *     "get",
 *     "put_user"={
 *      "method"="PUT",
 *     "route_name"="put_user",
 *      "deserialize"=false,
 *             "swagger_context"={
 *                 "consumes"={
 *                     "multipart/form-data",
 *                 },
 *                 "parameters"={
 *                     {
 *                         "in"="formData",
 *                         "name"="file",
 *                         "type"="file",
 *                         "description"="The file to upload",
 *                     },
 *                 },
 *             },
 *     },
 *     "delete"={"path"="/users/{id}",  "access_control"="(is_granted('ROLE_AdminSystem'))"},
 *     "get",
 *     }
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user:read","account:read","depot:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     *  @Groups({"user:read","users:write"})
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     * @Groups({"user:read"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups({"users:write"})
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Groups({"user:read","users:write","depot:read"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Groups({"user:read","users:write"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="string")
     *  @Groups({"user:read","users:write"})
     */
    private $phone;

    /**
     * @ORM\Column(type="string")
     *  @Groups({"user:read","users:write"})
     */
    private $cni;

    /**
     * @ORM\Column(type="blob", nullable=true)
     *  @Groups({"user:read","users:write","account:read"})
     */
    private $avatar;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isArchived=false;

    /**
     * @ORM\ManyToOne(targetEntity=Profil::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     *  @Groups({"user:read"})
     */
    private $profil;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Groups({"user:read","users:write"})
     *
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity=Deposit::class, mappedBy="user")
     * @ApiSubresource()
     */
    private $depot;

    /**
     * @ORM\ManyToOne(targetEntity=Agency::class, inversedBy="users")
     * @Groups({"user:read"})
     */
    private $agency;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="userDepot")
     */
    private $transactions;

    public function __construct()
    {
        $this->depot = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles["role"] = 'ROLE_'.$this->profil->getLibelle();

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
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

    public function getAvatar()
    {
        if ($this->avatar){
            return base64_encode(stream_get_contents( $this->avatar));
        }
        return null;
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;

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

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): self
    {
        $this->profil = $profil;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

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
            $depot->setUser($this);
        }

        return $this;
    }

    public function removeDepot(Deposit $depot): self
    {
        if ($this->depot->removeElement($depot)) {
            // set the owning side to null (unless already changed)
            if ($depot->getUser() === $this) {
                $depot->setUser(null);
            }
        }

        return $this;
    }

    public function getAgency(): ?Agency
    {
        return $this->agency;
    }

    public function setAgency(?Agency $agency): self
    {
        $this->agency = $agency;

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
            $transaction->setUserDepot($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getUserDepot() === $this) {
                $transaction->setUserDepot(null);
            }
        }

        return $this;
    }
}
