<?php

namespace App\Application\Tariff\Dto\Response;

use App\Entity\Tariff;

final readonly class TariffResponseDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $monthlyPrice,
        public int $coverageAmount,
        public ?int $deductible,
        public int $score,
        public string $productName,
        public string $providerName,
    ) {
    }

    public static function fromEntity(Tariff $tariff): self
    {
        $product = $tariff->getProduct();
        $provider = $product->getProvider();

        return new self(
            id: $tariff->getId(),
            name: $tariff->getName(),
            monthlyPrice: $tariff->getMonthlyPrice(),
            coverageAmount: $tariff->getCoverageAmount(),
            deductible: $tariff->getDeductible(),
            score: $tariff->getScore(),
            productName: $product->getName(),
            providerName: $provider->getName(),
        );
    }
}
