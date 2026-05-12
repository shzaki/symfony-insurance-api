<?php

namespace App\Entity;

use App\Repository\TariffRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TariffRepository::class)]
class Tariff
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name = '';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $monthlyPrice = '0.00';

    #[ORM\Column]
    private int $coverageAmount = 0;

    #[ORM\Column(nullable: true)]
    private ?int $deductible = null;

    #[ORM\Column]
    private int $score = 0;

    #[ORM\Column]
    private bool $isActive = false;

    #[ORM\ManyToOne(inversedBy: 'tariffs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?InsuranceProduct $product = null;

    /**
     * @var Collection<int, ComparisonResult>
     */
    #[ORM\OneToMany(targetEntity: ComparisonResult::class, mappedBy: 'tariff')]
    private Collection $comparisonResults;

    public function __construct()
    {
        $this->comparisonResults = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
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

    public function getCoverageAmount(): int
    {
        return $this->coverageAmount;
    }

    public function setCoverageAmount(int $coverageAmount): static
    {
        $this->coverageAmount = $coverageAmount;

        return $this;
    }

    public function getDeductible(): ?int
    {
        return $this->deductible;
    }

    public function setDeductible(?int $deductible): static
    {
        $this->deductible = $deductible;

        return $this;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getProduct(): ?InsuranceProduct
    {
        return $this->product;
    }

    public function setProduct(?InsuranceProduct $product): static
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Collection<int, ComparisonResult>
     */
    public function getComparisonResults(): Collection
    {
        return $this->comparisonResults;
    }

    public function addComparisonResult(ComparisonResult $comparisonResult): static
    {
        if (!$this->comparisonResults->contains($comparisonResult)) {
            $this->comparisonResults->add($comparisonResult);
            $comparisonResult->setTariff($this);
        }

        return $this;
    }

    public function removeComparisonResult(ComparisonResult $comparisonResult): static
    {
        if ($this->comparisonResults->removeElement($comparisonResult)) {
            if ($comparisonResult->getTariff() === $this) {
                $comparisonResult->setTariff(null);
            }
        }

        return $this;
    }
}
