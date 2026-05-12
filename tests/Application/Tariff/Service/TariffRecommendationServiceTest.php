<?php

namespace App\Tests\Application\Tariff\Service;

use App\Application\Tariff\Dto\Request\BestTariffsRequestDto;
use App\Application\Tariff\Service\TariffRecommendationService;
use App\Entity\Tariff;
use App\Repository\TariffRepository;
use PHPUnit\Framework\TestCase;

final class TariffRecommendationServiceTest extends TestCase
{
    public function testItReturnsBestTariffsFromRepository(): void
    {
        $expectedTariffs = [
            new Tariff(),
            new Tariff(),
        ];

        $tariffRepository = $this->createMock(TariffRepository::class);

        $tariffRepository
            ->expects($this->once())
            ->method('findBestActiveTariffs')
            ->with('building', null, 10)
            ->willReturn($expectedTariffs);

        $requestDto = new BestTariffsRequestDto();

        $service = new TariffRecommendationService($tariffRepository);

        $result = $service->findBestTariffs($requestDto);

        self::assertSame($expectedTariffs, $result);
    }
}
