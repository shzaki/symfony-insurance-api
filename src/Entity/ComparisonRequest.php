<?php

namespace App\Entity;

use App\Repository\ComparisonRequestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComparisonRequestRepository::class)]
class ComparisonRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $zipcode = null;

    #[ORM\Column]
    private ?int $buildingYear = null;

    #[ORM\Column]
    private ?int $livingArea = null;

    #[ORM\Column(length: 50)]
    private ?string $buildingType = null;

    #[ORM\Column]
    private ?bool $hasGarage = null;

    #[ORM\Column]
    private ?bool $hasSolarPanels = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, ComparisonResult>
     */
    #[ORM\OneToMany(targetEntity: ComparisonResult::class, mappedBy: 'comparisonRequest')]
    private Collection $comparisonResults;

    public function __construct()
    {
        $this->comparisonResults = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): static
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getBuildingYear(): ?int
    {
        return $this->buildingYear;
    }

    public function setBuildingYear(int $buildingYear): static
    {
        $this->buildingYear = $buildingYear;

        return $this;
    }

    public function getLivingArea(): ?int
    {
        return $this->livingArea;
    }

    public function setLivingArea(int $livingArea): static
    {
        $this->livingArea = $livingArea;

        return $this;
    }

    public function getBuildingType(): ?string
    {
        return $this->buildingType;
    }

    public function setBuildingType(string $buildingType): static
    {
        $this->buildingType = $buildingType;

        return $this;
    }

    public function hasGarage(): ?bool
    {
        return $this->hasGarage;
    }

    public function setHasGarage(bool $hasGarage): static
    {
        $this->hasGarage = $hasGarage;

        return $this;
    }

    public function hasSolarPanels(): ?bool
    {
        return $this->hasSolarPanels;
    }

    public function setHasSolarPanels(bool $hasSolarPanels): static
    {
        $this->hasSolarPanels = $hasSolarPanels;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

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
            $comparisonResult->setComparisonRequest($this);
        }

        return $this;
    }

    public function removeComparisonResult(ComparisonResult $comparisonResult): static
    {
        if ($this->comparisonResults->removeElement($comparisonResult)) {
            // set the owning side to null (unless already changed)
            if ($comparisonResult->getComparisonRequest() === $this) {
                $comparisonResult->setComparisonRequest(null);
            }
        }

        return $this;
    }
}
