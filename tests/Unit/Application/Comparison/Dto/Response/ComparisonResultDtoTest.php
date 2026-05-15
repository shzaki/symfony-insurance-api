<?php

namespace App\Tests\Unit\Application\Comparison\Dto\Response;

use App\Application\Comparison\Dto\Response\ComparisonResultDto;
use App\Entity\ComparisonResult;
use App\Entity\InsuranceProduct;
use App\Entity\InsuranceProvider;
use App\Entity\Tariff;
use App\Tests\Traits\EntityIdTrait;
use PHPUnit\Framework\TestCase;

final class ComparisonResultDtoTest extends TestCase
{
    use EntityIdTrait;

    public function testItMapsEntityToDto(): void
    {
        $provider = new InsuranceProvider();
        $provider->setName('Allianz');

        $product = new InsuranceProduct();
        $product->setName('Building Insurance');
        $product->setProvider($provider);

        $tariff = new Tariff();
        $tariff->setName('Premium');
        $tariff->setProduct($product);

        $comparisonResult = new ComparisonResult();
        self::setEntityId($comparisonResult, 1);
        $comparisonResult->setTariff($tariff);
        $comparisonResult->setMonthlyPrice('39.50');
        $comparisonResult->setYearlyPrice(null);
        $comparisonResult->setRankingScore(96);
        $comparisonResult->setRiskLevel('medium');
        $comparisonResult->setRecommendationReason(null);

        $comparisonResultDto = ComparisonResultDto::fromEntity($comparisonResult);

        self::assertSame(1, $comparisonResultDto->id);
        self::assertSame('39.50', $comparisonResultDto->monthlyPrice);
        self::assertSame(96, $comparisonResultDto->rankingScore);
        self::assertSame('medium', $comparisonResultDto->riskLevel);
        self::assertSame('Premium', $comparisonResultDto->tariffName);
        self::assertSame('Allianz', $comparisonResultDto->providerName);
    }

    public function testItConvertsDtoToArray(): void
    {
        $comparisonResultDto = new ComparisonResultDto(
            id: 1,
            monthlyPrice: '39.50',
            yearlyPrice: null,
            rankingScore: 96,
            riskLevel: 'medium',
            recommendationReason: null,
            tariffName: 'Premium',
            providerName: 'Allianz',
        );

        $result = $comparisonResultDto->toArray();

        self::assertSame(1, $result['id']);
        self::assertSame('39.50', $result['monthlyPrice']);
        self::assertSame(96, $result['rankingScore']);
        self::assertSame('Premium', $result['tariffName']);
        self::assertSame('Allianz', $result['providerName']);
    }

    public function testItThrowsExceptionWhenComparisonResultHasNoTariff(): void
    {
        $comparisonResult = new ComparisonResult();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('ComparisonResult has no tariff');

        ComparisonResultDto::fromEntity($comparisonResult);
    }

    public function testItThrowsExceptionWhenTariffHasNoProvider(): void
    {
        $product = new InsuranceProduct();
        $product->setName('Building Insurance');

        $tariff = new Tariff();
        $tariff->setName('Premium');
        $tariff->setProduct($product);

        $comparisonResult = new ComparisonResult();
        $comparisonResult->setTariff($tariff);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Tariff has no provider');

        ComparisonResultDto::fromEntity($comparisonResult);
    }
}
