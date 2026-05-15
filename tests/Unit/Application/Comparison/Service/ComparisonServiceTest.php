<?php

namespace App\Tests\Unit\Application\Comparison\Service;

use App\Application\Comparison\Dto\Request\ComparisonRequestDto;
use App\Application\Comparison\Dto\Response\ComparisonResponseDto;
use App\Application\Comparison\Dto\Response\ComparisonResultDto;
use App\Application\Comparison\Service\ComparisonService;
use App\Entity\InsuranceProduct;
use App\Entity\InsuranceProvider;
use App\Entity\Tariff;
use App\Repository\TariffRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class ComparisonServiceTest extends TestCase
{
    public function testItReturnsComparisonResults(): void
    {
        $tariffs = [
            self::createTariff('Premium', 'Allianz', '39.50', 96),
            self::createTariff('Comfort', 'AXA', '29.90', 88),
        ];

        $tariffRepository = $this->createMock(TariffRepository::class);

        $tariffRepository
            ->expects($this->once())
            ->method('findActiveTariffs')
            ->with(
                'building',
                null,
                10,
                1,
                'score',
                'desc',
            )
            ->willReturn($tariffs);

        $entityManager = $this->createMock(EntityManagerInterface::class);

        $entityManager
            ->expects($this->once())
            ->method('wrapInTransaction')
            ->willReturnCallback(static function (callable $callback): void {
                $callback();
            });

        $entityManager
            ->expects($this->exactly(3))
            ->method('persist');

        $entityManager
            ->expects($this->once())
            ->method('flush');

        $service = new ComparisonService(
            $tariffRepository,
            $entityManager,
        );

        $comparisonRequestDto = new ComparisonRequestDto(
            zipcode: '80331',
            buildingYear: 2018,
            livingArea: 120,
            buildingType: 'house',
            hasGarage: true,
            hasSolarPanels: false,
        );

        $comparisonResponseDto = $service->compare($comparisonRequestDto);

        self::assertInstanceOf(ComparisonResponseDto::class, $comparisonResponseDto);
        self::assertSame(2, $comparisonResponseDto->totalComparisonResults);
        self::assertCount(2, $comparisonResponseDto->results);
        self::assertContainsOnlyInstancesOf(ComparisonResultDto::class, $comparisonResponseDto->results);

        self::assertSame('Premium', $comparisonResponseDto->results[0]->tariffName);
        self::assertSame('Allianz', $comparisonResponseDto->results[0]->providerName);
        self::assertSame('39.50', $comparisonResponseDto->results[0]->monthlyPrice);

        self::assertSame('Comfort', $comparisonResponseDto->results[1]->tariffName);
        self::assertSame('AXA', $comparisonResponseDto->results[1]->providerName);
        self::assertSame('29.90', $comparisonResponseDto->results[1]->monthlyPrice);
    }

    private static function createTariff(
        string $tariffName,
        string $providerName,
        string $monthlyPrice,
        int $score,
    ): Tariff {
        $provider = new InsuranceProvider();
        $provider->setName($providerName);

        $product = new InsuranceProduct();
        $product->setName('Building Insurance');
        $product->setProvider($provider);

        $tariff = new Tariff();
        $tariff->setName($tariffName);
        $tariff->setMonthlyPrice($monthlyPrice);
        $tariff->setScore($score);
        $tariff->setProduct($product);

        return $tariff;
    }
}
