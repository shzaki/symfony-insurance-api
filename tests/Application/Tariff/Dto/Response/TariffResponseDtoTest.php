<?php

namespace App\Tests\Application\Tariff\Dto\Response;

use App\Application\Tariff\Dto\Response\TariffResponseDto;
use App\Entity\InsuranceProduct;
use App\Entity\InsuranceProvider;
use App\Entity\Tariff;
use App\Tests\Traits\EntityIdTrait;
use PHPUnit\Framework\TestCase;

final class TariffResponseDtoTest extends TestCase
{
    use EntityIdTrait;

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

    public function testItThrowsExceptionWhenTariffHasNoProduct(): void
    {
        $tariff = new Tariff();
        self::setEntityId($tariff, 1);
        $tariff->setName('Premium');
        $tariff->setMonthlyPrice('39.90');
        $tariff->setCoverageAmount(1000000);
        $tariff->setDeductible(250);
        $tariff->setScore(95);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Tariff has no product');

        TariffResponseDto::fromEntity($tariff);
    }

    public function testItThrowsExceptionWhenProductHasNoProvider(): void
    {
        $product = new InsuranceProduct();
        $product->setName('Premium Building Insurance');

        $tariff = new Tariff();
        self::setEntityId($tariff, 1);
        $tariff->setName('Premium');
        $tariff->setMonthlyPrice('39.90');
        $tariff->setCoverageAmount(1000000);
        $tariff->setDeductible(250);
        $tariff->setScore(95);
        $tariff->setProduct($product);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Product has no provider');

        TariffResponseDto::fromEntity($tariff);
    }
}
