<?php

namespace App\Entity;

use App\Repository\ComissionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ComissionsRepository::class)
 */
class Comissions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $state;

    /**
     * @ORM\Column(type="integer")
     */
    private $depositOperator;

    /**
     * @ORM\Column(type="integer")
     */
    private $withdrawalOperator;

    /**
     * @ORM\Column(type="integer")
     */
    private $applicationSystem;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getDepositOperator(): ?int
    {
        return $this->depositOperator;
    }

    public function setDepositOperator(int $depositOperator): self
    {
        $this->depositOperator = $depositOperator;

        return $this;
    }

    public function getWithdrawalOperator(): ?int
    {
        return $this->withdrawalOperator;
    }

    public function setWithdrawalOperator(int $withdrawalOperator): self
    {
        $this->withdrawalOperator = $withdrawalOperator;

        return $this;
    }

    public function getApplicationSystem(): ?int
    {
        return $this->applicationSystem;
    }

    public function setApplicationSystem(int $applicationSystem): self
    {
        $this->applicationSystem = $applicationSystem;

        return $this;
    }
}
