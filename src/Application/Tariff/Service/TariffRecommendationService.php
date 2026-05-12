<?php

namespace App\Application\Tariff\Service;

use App\Application\Tariff\Dto\Request\BestTariffsRequestDto;
use App\Repository\TariffRepository;

final readonly class TariffRecommendationService
{
    public function __construct(
        private TariffRepository $tariffRepository,
    ) {
    }

    /**
     * @return array<int, mixed>
     */
    public function findBestTariffs(BestTariffsRequestDto $requestDto): array
    {
        return $this->tariffRepository->findBestActiveTariffs(
            productType: $requestDto->productType,
            providerCode: $requestDto->providerCode,
            limit: $requestDto->limit,
        );
    }
}
