<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Situation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="situations")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseOrder", inversedBy="situations")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $purchaseOrder;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     */
    private $editedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime("now");
        $this->editedAt = $this->createdAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): void
    {
        $owner->addSituation($this);
        $this->owner = $owner;
    }

    public function getPurchaseOrder(): PurchaseOrder
    {
        return $this->purchaseOrder;
    }

    public function setPurchaseOrder(PurchaseOrder $purchaseOrder): void
    {
        $purchaseOrder->addSituation($this);
        $this->purchaseOrder = $purchaseOrder;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getEditedAt(): \DateTimeInterface
    {
        return $this->editedAt;
    }

    public function setEditedAt(\DateTime $editedAt): void
    {
        $this->editedAt = $editedAt;
    }
}
