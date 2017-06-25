<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Ldap\Adapter\ExtLdap\Collection;

class PurchaseOrderRepository extends EntityRepository
{
    /**
     * @return Collection|PurchaseOrder[]
     */
    public function findAllWithSuppliers(bool $isSettled): ?Collection
    {
        $qb = $this->createQueryBuilder('po')
            ->leftJoin('po.supplier', 's')
            ->addSelect('s')
            ->addOrderBy('po.number', 'ASC');

        if (!$isSettled) {
            $qb->andWhere('po.settled = 0');
        }

        return $qb
            ->getQuery()
            ->execute();
    }

    public function findOneById(int $purchaseOrderId): ?PurchaseOrder
    {
        return $this->createQueryBuilder('po')
            ->leftJoin('po.supplier', 's')
            ->addSelect('s')
            ->andWhere('po.id = :id')
            ->setParameter('id', $purchaseOrderId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Collection|PurchaseOrder[]
     */
    public function findAllWithBalances(): ?Collection
    {
        return $this->createQueryBuilder('po')
            ->innerJoin('po.balances', 'b')
            ->addSelect('b')
            ->getQuery()
            ->execute();
    }
}
