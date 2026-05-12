<?php

namespace App\Entity;

use App\Repository\ComparisonResultRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComparisonResultRepository::class)]
class ComparisonResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $monthlyPrice = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $yearlyPrice = null;

    #[ORM\Column]
    private int $rankingScore = 0;

    #[ORM\Column(length: 20)]
    private string $riskLevel = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $recommendationReason = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(inversedBy: 'comparisonResults')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ComparisonRequest $comparisonRequest = null;

    #[ORM\ManyToOne(inversedBy: 'comparisonResults')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tariff $tariff = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMonthlyPrice(): string
    {
        return $this->monthlyPrice;
    }

    public function setMonthlyPrice(string $monthlyPrice): static
    {
        $this->monthlyPrice = $monthlyPrice;

        return $this;
    }

    public function getYearlyPrice(): ?string
    {
        return $this->yearlyPrice;
    }

    public function setYearlyPrice(?string $yearlyPrice): static
    {
        $this->yearlyPrice = $yearlyPrice;

        return $this;
    }

    public function getRankingScore(): int
    {
        return $this->rankingScore;
    }

    public function setRankingScore(int $rankingScore): static
    {
        $this->rankingScore = $rankingScore;

        return $this;
    }

    public function getRiskLevel(): string
    {
        return $this->riskLevel;
    }

    public function setRiskLevel(string $riskLevel): static
    {
        $this->riskLevel = $riskLevel;

        return $this;
    }

    public function getRecommendationReason(): ?string
    {
        return $this->recommendationReason;
    }

    public function setRecommendationReason(?string $recommendationReason): static
    {
        $this->recommendationReason = $recommendationReason;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getComparisonRequest(): ?ComparisonRequest
    {
        return $this->comparisonRequest;
    }

    public function setComparisonRequest(?ComparisonRequest $comparisonRequest): static
    {
        $this->comparisonRequest = $comparisonRequest;

        return $this;
    }

    public function getTariff(): ?Tariff
    {
        return $this->tariff;
    }

    public function setTariff(?Tariff $tariff): static
    {
        $this->tariff = $tariff;

        return $this;
    }
}
