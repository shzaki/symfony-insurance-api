<?php

namespace App\Tests\Application\Tariff\Dto\Response;

use App\Application\Tariff\Dto\Response\TariffResponseDto;
use App\Entity\InsuranceProduct;
use App\Entity\InsuranceProvider;
use App\Entity\Tariff;
use PHPUnit\Framework\TestCase;

final class TariffResponseDtoTest extends TestCase
{
    public function testItMapsTariffEntityToResponseDto(): void
    {
        $provider = new InsuranceProvider();
        $provider->setName('Allianz');

        $product = new InsuranceProduct();
        $product->setName('Premium Building Insurance');
        $product->setProvider($provider);

        $tariff = new Tariff();
        self::setEntityId($tariff, 1);
        $tariff->setName('Premium');
        $tariff->setMonthlyPrice('39.90');
        $tariff->setCoverageAmount(1000000);
        $tariff->setDeductible(250);
        $tariff->setScore(95);
        $tariff->setProduct($product);

        $dto = TariffResponseDto::fromEntity($tariff);

        self::assertSame(1, $dto->id);
        self::assertSame('Premium', $dto->name);
        self::assertSame('39.90', $dto->monthlyPrice);
        self::assertSame(1000000, $dto->coverageAmount);
        self::assertSame(250, $dto->deductible);
        self::assertSame(95, $dto->score);
        self::assertSame('Premium Building Insurance', $dto->productName);
        self::assertSame('Allianz', $dto->providerName);
    }

    private static function setEntityId(object $entity, int $id): void
    {
        $reflectionProperty = new \ReflectionProperty($entity, 'id');
        $reflectionProperty->setValue($entity, $id);
    }
}
