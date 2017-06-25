<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Ldap\Adapter\ExtLdap\Collection;

class OrderRepository extends EntityRepository
{
    /**
     * @return Collection|Order[]
     */
    public function findAllWithSuppliers(bool $isSettled): ?Collection
    {
        $qb = $this->createQueryBuilder('o')
            ->leftJoin('o.supplier', 's')
            ->addSelect('s')
            ->addOrderBy('o.number', 'ASC');

        if (!$isSettled) {
            $qb->andWhere('o.settled = 0');
        }

        return $qb
            ->getQuery()
            ->execute();
    }

    public function findOneById(int $orderId): ?Order
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.supplier', 's')
            ->addSelect('s')
            ->andWhere('o.id = :id')
            ->setParameter('id', $orderId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Collection|Order[]
     */
    public function findAllWithBalances(): ?Collection
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.balances', 'b')
            ->addSelect('b')
            ->getQuery()
            ->execute();
    }
}
