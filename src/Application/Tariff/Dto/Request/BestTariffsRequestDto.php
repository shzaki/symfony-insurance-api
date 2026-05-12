<?php

namespace App\Application\Tariff\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class BestTariffsRequestDto
{
    private const MAX_LIMIT = 50;
    private const DEFAULT_PRODUCT_TYPE = 'building';
    private const ALLOWED_PRODUCT_TYPES = [self::DEFAULT_PRODUCT_TYPE];

    public function __construct(
        #[Assert\Positive]
        #[Assert\LessThanOrEqual(self::MAX_LIMIT)]
        public int $limit = 10,

        #[Assert\Choice(choices: self::ALLOWED_PRODUCT_TYPES)]
        public string $productType = self::DEFAULT_PRODUCT_TYPE,

        #[Assert\Length(max: 50)]
        #[Assert\Regex(pattern: '/^[A-Z0-9_-]+$/i')]
        public ?string $providerCode = null,
    ) {
    }

    public static function fromQuery(array $query): self
    {
        return new self(
            limit: isset($query['limit'])
                ? filter_var($query['limit'], FILTER_VALIDATE_INT, ['options' => ['default' => 0]])
                : 10,
            productType: isset($query['productType']) ? (string) $query['productType'] : self::DEFAULT_PRODUCT_TYPE,
            providerCode: isset($query['provider']) && $query['provider'] !== '' ? (string) $query['provider'] : null,
        );
    }
}
