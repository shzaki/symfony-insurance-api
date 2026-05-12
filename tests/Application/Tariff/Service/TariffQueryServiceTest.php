<?php

namespace App\Tests\Application\Tariff\Service;

use App\Application\Tariff\Dto\Request\TariffQueryRequestDto;
use App\Application\Tariff\Service\TariffQueryService;
use App\Entity\Tariff;
use App\Entity\InsuranceProduct;
use App\Entity\InsuranceProvider;
use App\Application\Tariff\Dto\Response\TariffResponseDto;
use App\Repository\TariffRepository;
use PHPUnit\Framework\TestCase;

final class TariffQueryServiceTest extends TestCase
{
    public function testItReturnsBestTariffsFromRepository(): void
    {
        $tariffs = [
            self::createTariff(1, 'Premium'),
            self::createTariff(2, 'Comfort'),
        ];

        $tariffRepository = $this->createMock(TariffRepository::class);

        $tariffRepository
            ->expects($this->once())
            ->method('findActiveTariffs')
            ->with('building', null, 10, 1, 'score', 'desc')
            ->willReturn($tariffs);

        $requestDto = new TariffQueryRequestDto();

        $service = new TariffQueryService($tariffRepository);

        $result = $service->getTariffs($requestDto);

        self::assertCount(2, $result);
        self::assertContainsOnlyInstancesOf(TariffResponseDto::class, $result);
        self::assertSame('Premium', $result[0]->name);
        self::assertSame('Comfort', $result[1]->name);
    }

    public function testItPassesRequestDtoFiltersPaginationAndSortingToRepository(): void
    {
        $tariffs = [
            self::createTariff(1, 'Premium'),
        ];

        $tariffRepository = $this->createMock(TariffRepository::class);

        $tariffRepository
            ->expects($this->once())
            ->method('findActiveTariffs')
            ->with('building', 'AXA', 5, 2, 'price', 'asc')
            ->willReturn($tariffs);

        $requestDto = new TariffQueryRequestDto(
            limit: 5,
            page: 2,
            sort: 'price',
            direction: 'asc',
            productType: 'building',
            providerCode: 'AXA',
        );

        $service = new TariffQueryService($tariffRepository);

        $result = $service->getTariffs($requestDto);

        self::assertCount(1, $result);
        self::assertContainsOnlyInstancesOf(TariffResponseDto::class, $result);
        self::assertSame('Premium', $result[0]->name);
    }

    private static function createTariff(int $id, string $name): Tariff
    {
        $provider = new InsuranceProvider();
        $provider->setName('Allianz');

        $product = new InsuranceProduct();
        $product->setName('Premium Building Insurance');
        $product->setProvider($provider);

        $tariff = new Tariff();
        self::setEntityId($tariff, $id);
        $tariff->setName($name);
        $tariff->setMonthlyPrice('39.90');
        $tariff->setCoverageAmount(1000000);
        $tariff->setDeductible(250);
        $tariff->setScore(95);
        $tariff->setProduct($product);

        return $tariff;
    }

    private static function setEntityId(object $entity, int $id): void
    {
        $reflectionProperty = new \ReflectionProperty($entity, 'id');
        $reflectionProperty->setValue($entity, $id);
    }
}
