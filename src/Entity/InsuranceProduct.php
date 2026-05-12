<?php

namespace App\Entity;

use App\Repository\InsuranceProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InsuranceProductRepository::class)]
class InsuranceProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\ManyToOne(inversedBy: 'insuranceProducts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?InsuranceProvider $provider = null;

    /**
     * @var Collection<int, Tariff>
     */
    #[ORM\OneToMany(targetEntity: Tariff::class, mappedBy: 'product')]
    private Collection $tariffs;

    public function __construct()
    {
        $this->tariffs = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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

    public function getProvider(): ?InsuranceProvider
    {
        return $this->provider;
    }

    public function setProvider(?InsuranceProvider $provider): static
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @return Collection<int, Tariff>
     */
    public function getTariffs(): Collection
    {
        return $this->tariffs;
    }

    public function addTariff(Tariff $tariff): static
    {
        if (!$this->tariffs->contains($tariff)) {
            $this->tariffs->add($tariff);
            $tariff->setProduct($this);
        }

        return $this;
    }

    public function removeTariff(Tariff $tariff): static
    {
        if ($this->tariffs->removeElement($tariff)) {
            if ($tariff->getProduct() === $this) {
                $tariff->setProduct(null);
            }
        }

        return $this;
    }
}
