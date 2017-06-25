<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;

class BalanceRepository extends EntityRepository
{
    /**
     * @return Collection|Balance[]
     */
    public function findAllByAccount(int $accountId): ?Collection
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.purchaseOrder', 'po')
            ->leftJoin('po.supplier', 's')
            ->addSelect('po')
            ->addSelect('s')
            ->andWhere('b.account = :accountId')
            ->setParameter('accountId', $accountId)
            ->addOrderBy('po.number', 'ASC')
            ->getQuery()
            ->execute();
    }

    /**
     * @return Collection|Balance[]
     */
    public function findAllByPurchaseOrder(int $purchaseOrderId): ?Collection
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.account', 'a')
            ->addSelect('a')
            ->andWhere('b.purchaseOrder = :id')
            ->setParameter('id', $purchaseOrderId)
            ->addOrderBy('a.number', 'ASC')
            ->getQuery()
            ->execute();
    }
}