<?php

namespace App\Application\Tariff\Service;

use App\Application\Tariff\Dto\Request\TariffQueryRequestDto;
use App\Application\Tariff\Dto\Response\TariffResponseDto;
use App\Application\Tariff\Exception\TariffNotFoundException;
use App\Repository\TariffRepository;

final readonly class TariffQueryService
{
    public function __construct(
        private TariffRepository $tariffRepository,
    ) {
    }

    /**
     * @return TariffResponseDto[]
     */
    public function getTariffs(TariffQueryRequestDto $requestDto): array
    {
        $tariffs = $this->tariffRepository->findActiveTariffs(
            productType: $requestDto->productType,
            providerCode: $requestDto->providerCode,
            limit: $requestDto->limit,
            page: $requestDto->page,
            sort: $requestDto->sort,
            direction: $requestDto->direction,
        );

        return array_map(
            static fn($tariff) => TariffResponseDto::fromEntity($tariff),
            $tariffs,
        );
    }

    public function getTariffById(int $id): TariffResponseDto
    {
        $tariff = $this->tariffRepository->findActiveTariffById($id);

        if ($tariff === null) {
            throw new TariffNotFoundException($id);
        }

        return TariffResponseDto::fromEntity($tariff);
    }
}
