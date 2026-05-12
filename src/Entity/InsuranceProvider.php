<?php

namespace App\Entity;

use App\Repository\InsuranceProviderRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: InsuranceProviderRepository::class)]
class InsuranceProvider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $code = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    /**
     * @var Collection<int, InsuranceProduct>
     */
    #[ORM\OneToMany(targetEntity: InsuranceProduct::class, mappedBy: 'provider')]
    private Collection $insuranceProducts;

    public function __construct()
    {
        $this->insuranceProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection<int, InsuranceProduct>
     */
    public function getInsuranceProducts(): Collection
    {
        return $this->insuranceProducts;
    }

    public function addInsuranceProduct(InsuranceProduct $insuranceProduct): static
    {
        if (!$this->insuranceProducts->contains($insuranceProduct)) {
            $this->insuranceProducts->add($insuranceProduct);
            $insuranceProduct->setProvider($this);
        }

        return $this;
    }

    public function removeInsuranceProduct(InsuranceProduct $insuranceProduct): static
    {
        if ($this->insuranceProducts->removeElement($insuranceProduct)) {
            if ($insuranceProduct->getProvider() === $this) {
                $insuranceProduct->setProvider(null);
            }
        }

        return $this;
    }
}
