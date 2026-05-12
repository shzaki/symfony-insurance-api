<?php

namespace App\Tests\Application\Tariff\Dto\Request;

use App\Application\Tariff\Dto\Request\TariffQueryRequestDto;
use PHPUnit\Framework\TestCase;

final class ListTariffsRequestDtoTest extends TestCase
{
    public function testItUsesDefaultValues(): void
    {
        $dto = TariffQueryRequestDto::fromQuery([]);

        self::assertSame(10, $dto->limit);
        self::assertSame(1, $dto->page);
        self::assertSame('score', $dto->sort);
        self::assertSame('desc', $dto->direction);
        self::assertSame('building', $dto->productType);
        self::assertNull($dto->providerCode);
    }

    public function testItMapsQueryValues(): void
    {
        $dto = TariffQueryRequestDto::fromQuery([
            'limit' => '25',
            'page' => '2',
            'sort' => 'price',
            'direction' => 'asc',
            'product_type' => 'building',
            'provider' => 'allianz',
        ]);

        self::assertSame(25, $dto->limit);
        self::assertSame(2, $dto->page);
        self::assertSame('price', $dto->sort);
        self::assertSame('asc', $dto->direction);
        self::assertSame('building', $dto->productType);
        self::assertSame('allianz', $dto->providerCode);
    }

    public function testInvalidIntegerFallsBackToZero(): void
    {
        $dto = TariffQueryRequestDto::fromQuery([
            'limit' => 'abc',
        ]);

        self::assertSame(10, $dto->limit);
    }
}
