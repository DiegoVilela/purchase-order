<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;

class PurchaseOrderRepository extends EntityRepository
{
    /**
     * @return PurchaseOrder[]
     */
    public function findAllWithSuppliers($isSettled = false)
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

    public function findOneById(int $purchaseOrderId)
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
     * @return PurchaseOrder[]
     */
    public function findAllWithBalances()
    {
        return $this->createQueryBuilder('po')
            ->innerJoin('po.balances', 'b')
            ->addSelect('b')
            ->getQuery()
            ->execute();
    }
}
