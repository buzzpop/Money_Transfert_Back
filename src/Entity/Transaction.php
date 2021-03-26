<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 * @ApiResource(
 *   attributes={
 *  "denormalization_context"={"groups"={"transaction:write"}},
 * },
 *     collectionOperations={
 *          "get",
 *     },
 *     itemOperations={
 *     "get"
 *     }
 * )
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"transaction:write","print","clients"})
     */
    private $amount;

    /**
     * @ORM\Column(type="date")
     * @Groups({"transaction:write","print","clients"})
     */
    private $depositDate;

    /**
     * @ORM\Column(type="date",nullable=true)
     * @Groups({"transaction:write"})
     */
    private $withdrawalDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"transaction:write"})
     */
    private $cancellationDate;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"transaction:write","print"})
     */
    private $taxes;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"transaction:write"})
     */
    private $stateTaxe;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"transaction:write"})
     */
    private $systemTaxe;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"transaction:write"})
     */
    private $shippingTaxe;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"transaction:write"})
     */
    private $withdrawalTaxe;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"transaction:write","print"})
     */
    private $transactionCode;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactions")
     * @Groups({"transaction:write"})
     * @ORM\JoinColumn(nullable=false)
     *
     */
    private $userDepot;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactions")
     */
    private $userRetrait;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"transaction:write","clients","print"})
     */
    private $clientDepot;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"clients","print"})
     */
    private $clientRetrait;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="transaction_depot")
     * @ORM\JoinColumn(nullable=false)
     */
    private $account_depot;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="transaction_retrait")
     */
    private $account_retrait;



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

    public function getDepositDate(): ?\DateTimeInterface
    {
        return  $this->depositDate;
    }

    public function setDepositDate(\DateTimeInterface $depositDate): self
    {
        $this->depositDate = $depositDate;

        return $this;
    }

    public function getWithdrawalDate(): ?\DateTimeInterface
    {
        return $this->withdrawalDate;
    }

    public function setWithdrawalDate(\DateTimeInterface $withdrawalDate): self
    {
        $this->withdrawalDate = $withdrawalDate;

        return $this;
    }

    public function getCancellationDate(): ?\DateTimeInterface
    {
        return $this->cancellationDate;
    }

    public function setCancellationDate(?\DateTimeInterface $cancellationDate): self
    {
        $this->cancellationDate = $cancellationDate;

        return $this;
    }

    public function getTaxes(): ?int
    {
        return $this->taxes;
    }

    public function setTaxes(int $taxes): self
    {
        $this->taxes = $taxes;

        return $this;
    }

    public function getStateTaxe(): ?int
    {
        return $this->stateTaxe;
    }

    public function setStateTaxe(int $stateTaxe): self
    {
        $this->stateTaxe = $stateTaxe;

        return $this;
    }

    public function getSystemTaxe(): ?int
    {
        return $this->systemTaxe;
    }

    public function setSystemTaxe(int $systemTaxe): self
    {
        $this->systemTaxe = $systemTaxe;

        return $this;
    }

    public function getShippingTaxe(): ?int
    {
        return $this->shippingTaxe;
    }

    public function setShippingTaxe(int $shippingTaxe): self
    {
        $this->shippingTaxe = $shippingTaxe;

        return $this;
    }

    public function getWithdrawalTaxe(): ?int
    {
        return $this->withdrawalTaxe;
    }

    public function setWithdrawalTaxe(int $withdrawalTaxe): self
    {
        $this->withdrawalTaxe = $withdrawalTaxe;

        return $this;
    }

    public function getTransactionCode(): ?string
    {
        return $this->transactionCode;
    }

    public function setTransactionCode(string $transactionCode): self
    {
        $this->transactionCode = $transactionCode;

        return $this;
    }

    public function getUserDepot(): ?User
    {
        return $this->userDepot;
    }

    public function setUserDepot(?User $userDepot): self
    {
        $this->userDepot = $userDepot;

        return $this;
    }

    public function getUserRetrait(): ?User
    {
        return $this->userRetrait;
    }

    public function setUserRetrait(?User $userRetrait): self
    {
        $this->userRetrait = $userRetrait;

        return $this;
    }

    public function getClientDepot(): ?Client
    {
        return $this->clientDepot;
    }

    public function setClientDepot(?Client $clientDepot): self
    {
        $this->clientDepot = $clientDepot;

        return $this;
    }

    public function getClientRetrait(): ?Client
    {
        return $this->clientRetrait;
    }

    public function setClientRetrait(?Client $clientRetrait): self
    {
        $this->clientRetrait = $clientRetrait;

        return $this;
    }

    public function getAccountDepot(): ?Account
    {
        return $this->account_depot;
    }

    public function setAccountDepot(?Account $account_depot): self
    {
        $this->account_depot = $account_depot;

        return $this;
    }

    public function getAccountRetrait(): ?Account
    {
        return $this->account_retrait;
    }

    public function setAccountRetrait(?Account $account_retrait): self
    {
        $this->account_retrait = $account_retrait;

        return $this;
    }



}
