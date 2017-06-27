<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @UniqueEntity("number")
 */
class Account
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", unique=TRUE)
     * @Assert\Range(min=622920101, max=631400000)
     */
    private $number;

    /**
     * @ORM\Column(length=43, unique=TRUE)
     * @Assert\Length(min=5, max=43)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Balance", mappedBy="account")
     */
    private $balances;

    public function __construct()
    {
        $this->balances = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setNumber(int $number)
    {
        $this->number = $number;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addBalance(Balance $balance)
    {
        $this->balances[] = $balance;
    }

    public function removeBalance(Balance $balance)
    {
        $this->balances->removeElement($balance);
    }

    /**
     * @return Collection|Balance[]
     */
    public function getBalances(): Collection
    {
        return $this->balances;
    }
}
