<?php

namespace App\Application\Comparison\Service;

use App\Application\Comparison\Dto\Request\ComparisonRequestDto;
use App\Application\Comparison\Dto\Response\ComparisonResponseDto;
use App\Application\Comparison\Dto\Response\ComparisonResultDto;
use App\Entity\ComparisonRequest;
use App\Entity\ComparisonResult;
use App\Entity\Tariff;
use App\Repository\TariffRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class ComparisonService
{
    private const string DEFAULT_RISK_LEVEL = 'medium';

    public function __construct(
        private TariffRepository $tariffRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function compare(ComparisonRequestDto $comparisonRequestDto): ComparisonResponseDto
    {
        $comparisonRequest = $this->buildComparisonRequest($comparisonRequestDto);

        $tariffs = $this->tariffRepository->findActiveTariffs(
            productType: 'building',
            limit: 10,
        );

        $comparisonResults = array_map(
            fn(Tariff $tariff) => $this->buildComparisonResult($comparisonRequest, $tariff),
            $tariffs,
        );

        $this->entityManager->wrapInTransaction(function () use ($comparisonRequest, $comparisonResults): void {
            $this->entityManager->persist($comparisonRequest);

            foreach ($comparisonResults as $comparisonResult) {
                $this->entityManager->persist($comparisonResult);
            }
            $this->entityManager->flush();
        });

        return new ComparisonResponseDto(
            comparisonId: (int) $comparisonRequest->getId(),
            totalComparisonResults: count($comparisonResults),
            results: array_map(
                static fn(ComparisonResult $comparisonResult) => ComparisonResultDto::fromEntity($comparisonResult),
                $comparisonResults,
            ),
        );
    }

    private function buildComparisonRequest(
        ComparisonRequestDto $comparisonRequestDto,
    ): ComparisonRequest {
        $comparisonRequest = new ComparisonRequest();
        $comparisonRequest->setZipcode($comparisonRequestDto->zipcode);
        $comparisonRequest->setBuildingYear($comparisonRequestDto->buildingYear);
        $comparisonRequest->setLivingArea($comparisonRequestDto->livingArea);
        $comparisonRequest->setBuildingType($comparisonRequestDto->buildingType);
        $comparisonRequest->setHasGarage($comparisonRequestDto->hasGarage);
        $comparisonRequest->setHasSolarPanels($comparisonRequestDto->hasSolarPanels);

        return $comparisonRequest;
    }

    private function buildComparisonResult(
        ComparisonRequest $comparisonRequest,
        Tariff $tariff,
    ): ComparisonResult {
        $comparisonResult = new ComparisonResult();
        $comparisonResult->setComparisonRequest($comparisonRequest);
        $comparisonResult->setTariff($tariff);
        $comparisonResult->setMonthlyPrice($tariff->getMonthlyPrice());
        $comparisonResult->setRankingScore($tariff->getScore());
        $comparisonResult->setRiskLevel(self::DEFAULT_RISK_LEVEL);

        return $comparisonResult;
    }
}
