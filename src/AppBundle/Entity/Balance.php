<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="BalanceRepository")
 * @UniqueEntity(fields={"account", "order"})
 */
class Balance
{
    /**
     * @ORM\Column(type="decimal", scale=2)
     * @Assert\NotBlank()
     * @Assert\Type("numeric")
     */
    private $amount;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="balances")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $account;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="balances")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $order;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     */
    private $date;

    public function __construct(float $amount, Account $account, Order $order)
    {
        $this->amount = $amount;
        $this->account = $account;
        $this->order = $order;
        $this->date = new \DateTime("now");
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setDate(\DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }
}
