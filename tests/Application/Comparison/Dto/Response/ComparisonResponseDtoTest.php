<?php

namespace App\Tests\Application\Comparison\Dto\Response;

use App\Application\Comparison\Dto\Response\ComparisonResponseDto;
use App\Application\Comparison\Dto\Response\ComparisonResultDto;
use PHPUnit\Framework\TestCase;

final class ComparisonResponseDtoTest extends TestCase
{
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

        $comparisonResponseDto = new ComparisonResponseDto(
            comparisonId: 7,
            totalComparisonResults: 1,
            results: [$comparisonResultDto],
        );

        $result = $comparisonResponseDto->toArray();

        self::assertSame(7, $result['comparisonId']);
        self::assertSame(1, $result['totalResults']);
        self::assertCount(1, $result['results']);

        self::assertSame(1, $result['results'][0]['id']);
        self::assertSame('39.50', $result['results'][0]['monthlyPrice']);
        self::assertSame('Premium', $result['results'][0]['tariffName']);
        self::assertSame('Allianz', $result['results'][0]['providerName']);
    }
}
