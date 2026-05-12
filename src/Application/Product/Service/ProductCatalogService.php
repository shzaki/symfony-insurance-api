<?php

namespace App\Application\Product\Service;

use App\Application\Product\Dto\Response\ProductResponseDto;
use App\Repository\InsuranceProductRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final readonly class ProductCatalogService
{
    private const CACHE_KEY_AVAILABLE_PRODUCTS = 'available_products';
    private const CACHE_KEY_AVAILABLE_PRODUCT_TYPES = 'available_product_types';

    private const CACHE_TTL_SECONDS = 24 * 60 * 60;

    public function __construct(
        private CacheInterface $cache,
        private InsuranceProductRepository $insuranceProductRepository,
    ) {
    }

    /**
     * @return ProductResponseDto[]
     */
    public function getProducts(): array
    {
        return $this->cache->get(
            self::CACHE_KEY_AVAILABLE_PRODUCTS,
            function (ItemInterface $item): array {
                $item->expiresAfter(self::CACHE_TTL_SECONDS);

                $products = $this->insuranceProductRepository->findAvailableProducts();

                return array_map(
                    static fn($product) => ProductResponseDto::fromEntity($product),
                    $products,
                );
            },
        );
    }

    /**
     * @return string[]
     */
    public function getProductTypes(): array
    {
        return $this->cache->get(
            self::CACHE_KEY_AVAILABLE_PRODUCT_TYPES,
            function (ItemInterface $item): array {
                $item->expiresAfter(self::CACHE_TTL_SECONDS);

                return $this->insuranceProductRepository->findAvailableProductTypes();
            },
        );
    }

    public function invalidateCatalogCache(): void
    {
        $this->cache->delete(self::CACHE_KEY_AVAILABLE_PRODUCTS);
        $this->cache->delete(self::CACHE_KEY_AVAILABLE_PRODUCT_TYPES);
    }
}
