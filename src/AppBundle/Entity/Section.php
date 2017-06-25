<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @UniqueEntity("name")
 */
class Section
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(length=20, unique=true)
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="PurchaseOrder", mappedBy="section")
     */
    private $purchaseOrders = null;

    public function __construct()
    {
        $this->purchaseOrders = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addPurchaseOrder(PurchaseOrder $order): void
    {
        $this->purchaseOrders[] = $order;
    }

    public function removePurchaseOrder(PurchaseOrder $order): void
    {
        $this->purchaseOrders->removeElement($order);
    }

    /**
     * @return Collection|PurchaseOrder[]
     */
    public function getPurchaseOrders(): Collection
    {
        return $this->purchaseOrders;
    }
}
