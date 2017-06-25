<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="OrderRepository")
 * @UniqueEntity("number")
 */
class Order
{
    const TYPE_ORDINARY = 'ordinary';
    const TYPE_ESTIMATED = 'estimated';
    const TYPE_GLOBAL = 'global';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(length=12, unique=TRUE)
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $number;

    /**
     * @ORM\Column(length=10, nullable=true)
     * @Assert\Choice({"ORDINARY", "ESTIMATED", "GLOBAL"})
     */
    private $type = self::TYPE_GLOBAL;

    /**
     * @ORM\Column(type="boolean")
     */
    private $settled = false;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     */
    private $editedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Supplier", inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $supplier;

    /**
     * @ORM\ManyToOne(targetEntity="Section", inversedBy="orders")
     * @ORM\JoinColumn(nullable=true)
     */
    private $section;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="orders")
     * @ORM\JoinColumn(nullable=true)
     */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity="Balance", mappedBy="order")
     */
    private $balances;

    /**
     * @ORM\OneToMany(targetEntity="Situation", mappedBy="order")
     */
    private $situations = null;

    public function __construct(string $number = null, Supplier $supplier = null)
    {
        $this->number    = $number;
        $this->supplier    = $supplier;
        $this->editedAt = new \DateTime("now");
        $this->balances    = new ArrayCollection();
        $this->situations = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isSettled(): bool
    {
        return $this->settled;
    }

    public function settle(): void
    {
        $this->settled = true;
    }

    public function undoSettle(): void
    {
        $this->settled = false;
    }

    public function setEditedAt()
    {
        $this->editedAt = new \DateTime("now");
    }

    public function getEditedAt(): \DateTimeInterface
    {
        return $this->editedAt;
    }

    public function setSupplier(Supplier $supplier): void
    {
        $supplier->addOrder($this);
        $this->supplier = $supplier;
    }

    public function getSupplier(): Supplier
    {
        return $this->supplier;
    }

    public function setSection(Section $section): void
    {
        $section->addOrder($this);
        $this->section = $section;
    }

    public function getSection(): Section
    {
        return $this->section;
    }

    public function setOwner(User $owner): void
    {
        $owner->addOrder($this);
        $this->owner = $owner;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function addBalance(Balance $balance): void
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

    public function addSituation(Situation $situation): void
    {
        $this->situations[] = $situation;
    }

    public function removeSituation(Situation $situation)
    {
        $this->situations->removeElement($situation);
    }

    /**
     * @return Collection|Situation[]
     */
    public function getSituations(): Collection
    {
        return $this->situations;
    }

    public function __toString(): string
    {
        return $this->number;
    }
}
