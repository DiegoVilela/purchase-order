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
            ->leftJoin('b.order', 'o')
            ->leftJoin('o.supplier', 's')
            ->addSelect('o')
            ->addSelect('s')
            ->andWhere('b.account = :accountId')
            ->setParameter('accountId', $accountId)
            ->addOrderBy('o.number', 'ASC')
            ->getQuery()
            ->execute();
    }

    /**
     * @return Collection|Balance[]
     */
    public function findAllByOrder(int $orderId): ?Collection
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.account', 'a')
            ->addSelect('a')
            ->andWhere('b.order = :id')
            ->setParameter('id', $orderId)
            ->addOrderBy('a.number', 'ASC')
            ->getQuery()
            ->execute();
    }
}