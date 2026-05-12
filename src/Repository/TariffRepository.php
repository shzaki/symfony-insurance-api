<?php

namespace App\Repository;

use App\Entity\Tariff;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tariff>
 */
class TariffRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tariff::class);
    }

    /**
     * @return Tariff[]
     */
    public function findBestActiveTariffs(
        string $productType = 'building',
        ?string $providerCode = null,
        int $limit = 50,
    ): array {
        $queryBuilder = $this->createQueryBuilder('tariff')
            ->select('tariff', 'product', 'provider')
            ->innerJoin('tariff.product', 'product')
            ->innerJoin('product.provider', 'provider')
            ->andWhere('tariff.isActive = :active')
            ->andWhere('product.isActive = :active')
            ->andWhere('provider.isActive = :active')
            ->andWhere('product.type = :productType')
            ->setParameter('active', true)
            ->setParameter('productType', $productType);

        if ($providerCode !== null) {
            $queryBuilder
                ->andWhere('provider.code = :providerCode')
                ->setParameter('providerCode', $providerCode);
        }

        return $queryBuilder
            ->orderBy('tariff.score', 'DESC')
            ->addOrderBy('tariff.monthlyPrice', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
