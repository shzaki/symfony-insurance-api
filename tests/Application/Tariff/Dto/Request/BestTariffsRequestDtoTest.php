<?php

namespace App\Tests\Application\Tariff\Dto\Request;

use App\Application\Tariff\Dto\Request\BestTariffsRequestDto;
use PHPUnit\Framework\TestCase;

final class BestTariffsRequestDtoTest extends TestCase
{
    public function testItUsesDefaultValues(): void
    {
        $dto = BestTariffsRequestDto::fromQuery([]);

        self::assertSame(10, $dto->limit);
        self::assertSame('building', $dto->productType);
        self::assertNull($dto->providerCode);
    }

    public function testItMapsQueryValues(): void
    {
        $dto = BestTariffsRequestDto::fromQuery([
            'limit' => '25',
            'productType' => 'building',
            'provider' => 'allianz',
        ]);

        self::assertSame(25, $dto->limit);
        self::assertSame('building', $dto->productType);
        self::assertSame('allianz', $dto->providerCode);
    }

    public function testInvalidIntegerFallsBackToZero(): void
    {
        $dto = BestTariffsRequestDto::fromQuery([
            'limit' => 'abc',
        ]);

        self::assertSame(0, $dto->limit);
    }
}
