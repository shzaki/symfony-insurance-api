<?php

namespace App\Application\Tariff\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class TariffQueryRequestDto
{
    private const DEFAULT_LIMIT = 10;
    private const MAX_LIMIT = 50;
    private const DEFAULT_PAGE = 1;

    private const DEFAULT_SORT = 'score';
    private const DEFAULT_DIRECTION = 'desc';
    private const DEFAULT_PRODUCT_TYPE = 'building';

    private const QUERY_PARAM_PROVIDER = 'provider';
    private const QUERY_PARAM_PRODUCT_TYPE = 'product_type';

    private const QUERY_PARAM_LIMIT = 'limit';
    private const QUERY_PARAM_PAGE = 'page';
    private const QUERY_PARAM_SORT = 'sort';
    private const QUERY_PARAM_DIRECTION = 'direction';

    private const ALLOWED_SORT_FIELDS = ['score', 'price', 'name'];
    private const ALLOWED_DIRECTIONS = ['asc', 'desc'];

    public function __construct(
        #[Assert\Positive]
        #[Assert\LessThanOrEqual(self::MAX_LIMIT)]
        public int $limit = self::DEFAULT_LIMIT,

        #[Assert\Positive]
        public int $page = self::DEFAULT_PAGE,

        #[Assert\Choice(choices: self::ALLOWED_SORT_FIELDS)]
        public string $sort = self::DEFAULT_SORT,

        #[Assert\Choice(choices: self::ALLOWED_DIRECTIONS)]
        public string $direction = self::DEFAULT_DIRECTION,

        public string $productType = self::DEFAULT_PRODUCT_TYPE,

        #[Assert\Length(max: 50)]
        #[Assert\Regex(pattern: '/^[A-Z0-9_-]+$/i')]
        public ?string $providerCode = null,
    ) {
    }

    public static function fromQuery(array $query): self
    {
        return new self(
            limit: self::integerFromQuery($query, self::QUERY_PARAM_LIMIT, self::DEFAULT_LIMIT),
            page: self::integerFromQuery($query, self::QUERY_PARAM_PAGE, self::DEFAULT_PAGE),
            sort: isset($query[self::QUERY_PARAM_SORT])
                ? strtolower((string) $query[self::QUERY_PARAM_SORT])
                : self::DEFAULT_SORT,
            direction: isset($query[self::QUERY_PARAM_DIRECTION])
                ? strtolower((string) $query[self::QUERY_PARAM_DIRECTION])
                : self::DEFAULT_DIRECTION,
            productType: isset($query[self::QUERY_PARAM_PRODUCT_TYPE])
                ? (string) $query[self::QUERY_PARAM_PRODUCT_TYPE]
                : self::DEFAULT_PRODUCT_TYPE,
            providerCode: isset($query[self::QUERY_PARAM_PROVIDER]) && $query[self::QUERY_PARAM_PROVIDER] !== ''
                ? (string) $query[self::QUERY_PARAM_PROVIDER]
                : null,
        );
    }

    private static function integerFromQuery(array $query, string $key, int $default): int
    {
        if (!isset($query[$key])) {
            return $default;
        }

        return filter_var(
            $query[$key],
            FILTER_VALIDATE_INT,
            ['options' => ['default' => $default]],
        );
    }
}

