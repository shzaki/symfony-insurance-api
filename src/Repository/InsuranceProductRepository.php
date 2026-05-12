<?php

namespace App\Repository;

use App\Entity\InsuranceProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class InsuranceProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InsuranceProduct::class);
    }

    /**
     * @return InsuranceProduct[]
     */
    public function findAvailableProducts(): array
    {
        return $this->createQueryBuilder('product')
            ->select('product', 'provider')
            ->innerJoin('product.provider', 'provider')
            ->andWhere('product.isActive = :active')
            ->andWhere('provider.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('product.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return string[]
     */
    public function findAvailableProductTypes(): array
    {
        $rows = $this->createQueryBuilder('product')
            ->select('DISTINCT product.type AS type')
            ->innerJoin('product.provider', 'provider')
            ->andWhere('product.isActive = :active')
            ->andWhere('provider.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('product.type', 'ASC')
            ->getQuery()
            ->getArrayResult();

        return array_column($rows, 'type');
    }
}
