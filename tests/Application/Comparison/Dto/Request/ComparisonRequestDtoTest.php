<?php

namespace App\Tests\Application\Comparison\Dto\Request;

use App\Application\Comparison\Dto\Request\ComparisonRequestDto;
use PHPUnit\Framework\TestCase;

final class ComparisonRequestDtoTest extends TestCase
{
    public function testItCreatesDtoFromArray(): void
    {
        $comparisonRequestDto = ComparisonRequestDto::fromArray([
            'zipcode' => '80331',
            'building_year' => 2018,
            'living_area' => 120,
            'building_type' => 'house',
            'has_garage' => true,
            'has_solar_panels' => false,
        ]);

        self::assertSame('80331', $comparisonRequestDto->zipcode);
        self::assertSame(2018, $comparisonRequestDto->buildingYear);
        self::assertSame(120, $comparisonRequestDto->livingArea);
        self::assertSame('house', $comparisonRequestDto->buildingType);
        self::assertTrue($comparisonRequestDto->hasGarage);
        self::assertFalse($comparisonRequestDto->hasSolarPanels);
    }

    public function testItUsesDefaultValuesWhenArrayKeysAreMissing(): void
    {
        $comparisonRequestDto = ComparisonRequestDto::fromArray([]);

        self::assertSame('', $comparisonRequestDto->zipcode);
        self::assertSame(0, $comparisonRequestDto->buildingYear);
        self::assertSame(0, $comparisonRequestDto->livingArea);
        self::assertSame('', $comparisonRequestDto->buildingType);
        self::assertFalse($comparisonRequestDto->hasGarage);
        self::assertFalse($comparisonRequestDto->hasSolarPanels);
    }
}
