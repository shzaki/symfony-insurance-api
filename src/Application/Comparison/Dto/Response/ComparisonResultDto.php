<?php

namespace App\Application\Comparison\Dto\Response;

use App\Entity\ComparisonResult;

final readonly class ComparisonResultDto
{
    public function __construct(
        public int     $id,
        public string  $monthlyPrice,
        public ?string $yearlyPrice,
        public int     $rankingScore,
        public string  $riskLevel,
        public ?string $recommendationReason,
        public string  $tariffName,
        public string  $providerName,
    ) {
    }

    public static function fromEntity(ComparisonResult $comparisonResult): self
    {
        $tariff = $comparisonResult->getTariff()
            ?? throw new \LogicException('ComparisonResult has no tariff');

        $provider = $tariff->getProduct()?->getProvider()
            ?? throw new \LogicException('Tariff has no provider');

        return new self(
            id: (int) $comparisonResult->getId(),
            monthlyPrice: $comparisonResult->getMonthlyPrice(),
            yearlyPrice: $comparisonResult->getYearlyPrice(),
            rankingScore: $comparisonResult->getRankingScore(),
            riskLevel: $comparisonResult->getRiskLevel(),
            recommendationReason: $comparisonResult->getRecommendationReason(),
            tariffName: $tariff->getName(),
            providerName: $provider->getName(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'monthlyPrice' => $this->monthlyPrice,
            'yearlyPrice' => $this->yearlyPrice,
            'rankingScore' => $this->rankingScore,
            'riskLevel' => $this->riskLevel,
            'recommendationReason' => $this->recommendationReason,
            'tariffName' => $this->tariffName,
            'providerName' => $this->providerName,
        ];
    }
}
