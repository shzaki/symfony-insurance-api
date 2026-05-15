<?php

namespace App\Tests\Integration;

use App\Entity\ComparisonRequest;
use App\Entity\ComparisonResult;
use App\Entity\InsuranceProduct;
use App\Entity\InsuranceProvider;
use App\Entity\Tariff;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class IntegrationTestCase extends WebTestCase
{
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        static::createClient();

        $container = static::getContainer();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get(EntityManagerInterface::class);

        $this->entityManager = $entityManager;

        $this->cleanDatabase();
    }

    private function cleanDatabase(): void
    {
        $this->entityManager
            ->createQuery('DELETE FROM ' . ComparisonResult::class . ' comparisonResult')
            ->execute();

        $this->entityManager
            ->createQuery('DELETE FROM ' . Tariff::class . ' tariff')
            ->execute();

        $this->entityManager
            ->createQuery('DELETE FROM ' . ComparisonRequest::class . ' comparisonRequest')
            ->execute();

        $this->entityManager
            ->createQuery('DELETE FROM ' . InsuranceProduct::class . ' product')
            ->execute();

        $this->entityManager
            ->createQuery('DELETE FROM ' . InsuranceProvider::class . ' provider')
            ->execute();
    }
}
