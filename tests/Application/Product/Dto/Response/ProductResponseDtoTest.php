<?php

namespace App\Tests\Application\Product\Dto\Response;

use App\Application\Product\Dto\Response\ProductResponseDto;
use App\Entity\InsuranceProduct;
use App\Entity\InsuranceProvider;
use PHPUnit\Framework\TestCase;

final class ProductResponseDtoTest extends TestCase
{
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

    private static function setEntityId(object $entity, int $id): void
    {
        $reflectionProperty = new \ReflectionProperty($entity, 'id');
        $reflectionProperty->setValue($entity, $id);
    }
}
