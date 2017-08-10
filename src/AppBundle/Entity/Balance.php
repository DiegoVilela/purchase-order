<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="BalanceRepository")
 * @UniqueEntity(fields={"account", "purchaseOrder"})
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
     * @ORM\ManyToOne(targetEntity="PurchaseOrder", inversedBy="balances")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $purchaseOrder;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     */
    private $date;

    public function __construct(float $amount, Account $account, PurchaseOrder $purchaseOrder)
    {
        $this->amount = $amount;
        $this->account = $account;
        $this->purchaseOrder = $purchaseOrder;
        $this->date = new \DateTime("now");
    }

    public function setAmount(float $amount)
    {
        $this->amount = $amount;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setDate(\DateTimeInterface $date)
    {
        $this->date = $date;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setAccount(Account $account)
    {
        $this->account = $account;
    }

    public function getAccount()
    {
        return $this->account;
    }

    public function setPurchaseOrder(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
    }

    public function getPurchaseOrder()
    {
        return $this->purchaseOrder;
    }
}
