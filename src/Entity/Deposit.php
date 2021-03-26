<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\DepositRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Annotation\ApiFilter;


/**
 * @ORM\Entity(repositoryClass=DepositRepository::class)
 *  *@ApiFilter(BooleanFilter::class, properties={"cancelled"})

 * @ApiResource(
 *     routePrefix="/admin",
 *     normalizationContext={"groups"={"depot:read"}},
 *     denormalizationContext={"groups"={"depot:write"}},
 *
 *     collectionOperations={
 *     "get"={
 *      "access_control"="(is_granted('ROLE_AdminSystem') or is_granted('ROLE_Caissier'))",
 *          "access_control_message"="Vous n'avez pas access à cette Ressource",
 *     },
 *      "post"={
 *          "access_control"="(is_granted('ROLE_AdminSystem') or is_granted('ROLE_Caissier'))",
 *          "access_control_message"="Vous n'avez pas access à cette Ressource",
 *     },
 *     },
 *      itemOperations={
 *     "get",
 *     "put"={
 *      "access_control"="(is_granted('ROLE_AdminSystem') or is_granted('ROLE_Caissier'))",
 *          "access_control_message"="Vous n'avez pas access à cette Ressource",
 *     }
 *     }
 * )
 */
class Deposit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"depot:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"depot:read","depot:write"})
     * @Assert\GreaterThanOrEqual(
     *     value="0",
     *     message="entrer une valeur positive"
     * )
     */
    private $amount;

    /**
     * @ORM\Column(type="date")
     * @Groups({"depot:read","depot:write"})
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="depot")
     * @ORM\JoinColumn(nullable=false)
     * @ApiSubresource()
     * @Groups({"depot:read"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="depot")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"depot:write"})
     */
    private $account;

    /**
     * @ORM\Column(type="boolean")
     *  * @Groups({"depot:read"})
     */
    private $cancelled=false;

    public function __construct(){
        $this->date= (new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getCancelled(): ?bool
    {
        return $this->cancelled;
    }

    public function setCancelled(bool $cancelled): self
    {
        $this->cancelled = $cancelled;

        return $this;
    }
}
