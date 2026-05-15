<?php

namespace App\Tests\Application\Product\Dto\Response;

use App\Application\Product\Dto\Response\ProductResponseDto;
use App\Entity\InsuranceProduct;
use App\Entity\InsuranceProvider;
use App\Tests\Traits\EntityIdTrait;
use PHPUnit\Framework\TestCase;

final class ProductResponseDtoTest extends TestCase
{
    use EntityIdTrait;

    public function testItMapsInsuranceProductEntityToResponseDto(): void
    {
        $provider = new InsuranceProvider();
        $provider->setName('Allianz');

        $product = new InsuranceProduct();
        self::setEntityId($product, 1);
        $product->setName('Premium Building Insurance');
        $product->setProvider($provider);

        $dto = ProductResponseDto::fromEntity($product);

        self::assertSame(1, $dto->id);
        self::assertSame('Premium Building Insurance', $dto->name);
        self::assertSame('Allianz', $dto->providerName);
    }
}
