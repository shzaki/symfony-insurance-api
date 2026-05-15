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
    public function findActiveTariffs(
        string $productType = 'building',
        ?string $providerCode = null,
        int $limit = 50,
        int $page = 1,
        string $sort = 'score',
        string $direction = 'desc',
    ): array {
        $sortMap = [
            'score' => 'tariff.score',
            'price' => 'tariff.monthlyPrice',
            'name' => 'tariff.name',
        ];

        $sortField = $sortMap[$sort] ?? $sortMap['score'];
        $sortDirection = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';
        $offset = ($page - 1) * $limit;

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
            ->orderBy($sortField, $sortDirection)
            ->addOrderBy('tariff.id', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findActiveTariffById(int $id): ?Tariff
    {
        return $this->createQueryBuilder('tariff')
            ->select('tariff', 'product', 'provider')
            ->innerJoin('tariff.product', 'product')
            ->innerJoin('product.provider', 'provider')
            ->andWhere('tariff.id = :id')
            ->andWhere('tariff.isActive = :active')
            ->setParameter('id', $id)
            ->setParameter('active', true)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
