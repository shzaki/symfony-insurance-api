<?php

namespace App\Application\Comparison\Dto\Request;

use App\Validator\Constraints\NotFutureYear;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ComparisonRequestDto
{
    private const REQUEST_FIELD_ZIPCODE = 'zipcode';
    private const REQUEST_FIELD_BUILDING_YEAR = 'building_year';
    private const REQUEST_FIELD_LIVING_AREA = 'living_area';
    private const REQUEST_FIELD_BUILDING_TYPE = 'building_type';
    private const REQUEST_FIELD_HAS_GARAGE = 'has_garage';
    private const REQUEST_FIELD_HAS_SOLAR_PANELS = 'has_solar_panels';

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 4, max: 20)]
        public string $zipcode,

        #[Assert\NotBlank]
        #[Assert\Range(min: 1800)]
        #[NotFutureYear]
        public int $buildingYear,

        #[Assert\NotBlank]
        #[Assert\Positive]
        public int $livingArea,

        #[Assert\NotBlank]
        #[Assert\Length(max: 50)]
        public string $buildingType,

        public bool $hasGarage = false,
        public bool $hasSolarPanels = false,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            zipcode: (string) ($data[self::REQUEST_FIELD_ZIPCODE] ?? ''),
            buildingYear: (int) ($data[self::REQUEST_FIELD_BUILDING_YEAR] ?? 0),
            livingArea: (int) ($data[self::REQUEST_FIELD_LIVING_AREA] ?? 0),
            buildingType: (string) ($data[self::REQUEST_FIELD_BUILDING_TYPE] ?? ''),
            hasGarage: (bool) ($data[self::REQUEST_FIELD_HAS_GARAGE] ?? false),
            hasSolarPanels: (bool) ($data[self::REQUEST_FIELD_HAS_SOLAR_PANELS] ?? false),
        );
    }
}
