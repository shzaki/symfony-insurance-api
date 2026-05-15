<?php

namespace App\Tests\Application\Product\Service;

use App\Application\Product\Dto\Response\ProductResponseDto;
use App\Application\Product\Service\ProductCatalogService;
use App\Entity\InsuranceProduct;
use App\Entity\InsuranceProvider;
use App\Repository\InsuranceProductRepository;
use App\Tests\Traits\EntityIdTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class ProductCatalogServiceTest extends TestCase
{
    use EntityIdTrait;

    public function testItReturnsAvailableProductsMappedToDtos(): void
    {
        $provider = new InsuranceProvider();
        $provider->setName('Allianz');

        $product = new InsuranceProduct();
        self::setEntityId($product, 1);
        $product->setName('Premium Building Insurance');
        $product->setProvider($provider);

        $repository = $this->createMock(InsuranceProductRepository::class);
        $repository
            ->expects($this->once())
            ->method('findAvailableProducts')
            ->willReturn([$product]);

        $cacheItem = $this->createMock(ItemInterface::class);
        $cacheItem
            ->expects($this->once())
            ->method('expiresAfter');

        $cache = $this->createMock(CacheInterface::class);
        $cache
            ->expects($this->once())
            ->method('get')
            ->with(
                'available_products',
                $this->callback(function (callable $callback) use ($cacheItem): bool {
                    $result = $callback($cacheItem);

                    self::assertCount(1, $result);
                    self::assertInstanceOf(ProductResponseDto::class, $result[0]);
                    self::assertSame('Premium Building Insurance', $result[0]->name);
                    self::assertSame('Allianz', $result[0]->providerName);

                    return true;
                }),
            )
            ->willReturn([
                new ProductResponseDto(
                    id: 1,
                    name: 'Premium Building Insurance',
                    providerName: 'Allianz',
                ),
            ]);

        $service = new ProductCatalogService($cache, $repository);

        $result = $service->getProducts();

        self::assertCount(1, $result);
        self::assertInstanceOf(ProductResponseDto::class, $result[0]);
    }

    public function testItReturnsAvailableProductTypes(): void
    {
        $repository = $this->createMock(InsuranceProductRepository::class);
        $repository
            ->expects($this->once())
            ->method('findAvailableProductTypes')
            ->willReturn(['building', 'car']);

        $cacheItem = $this->createMock(ItemInterface::class);
        $cacheItem
            ->expects($this->once())
            ->method('expiresAfter');

        $cache = $this->createMock(CacheInterface::class);
        $cache
            ->expects($this->once())
            ->method('get')
            ->with(
                'available_product_types',
                $this->callback(function (callable $callback) use ($cacheItem): bool {
                    $result = $callback($cacheItem);

                    self::assertSame(['building', 'car'], $result);

                    return true;
                }),
            )
            ->willReturn(['building', 'car']);

        $service = new ProductCatalogService($cache, $repository);

        $result = $service->getProductTypes();

        self::assertSame(['building', 'car'], $result);
    }

    public function testItReturnsCachedAvailableProductsWithoutCallingRepository(): void
    {
        $cachedProducts = [
            new ProductResponseDto(
                id: 1,
                name: 'Cached Building Insurance',
                providerName: 'Allianz',
            ),
        ];

        $repository = $this->createMock(InsuranceProductRepository::class);
        $repository
            ->expects($this->never())
            ->method('findAvailableProducts');

        $cache = $this->createMock(CacheInterface::class);
        $cache
            ->expects($this->once())
            ->method('get')
            ->with(
                'available_products',
                $this->callback(static fn($value): bool => is_callable($value)),
            )
            ->willReturn($cachedProducts);

        $service = new ProductCatalogService($cache, $repository);

        $result = $service->getProducts();

        self::assertSame($cachedProducts, $result);
    }

    public function testItInvalidatesProductCache(): void
    {
        $repository = $this->createStub(InsuranceProductRepository::class);

        $cache = $this->createMock(CacheInterface::class);
        $cache
            ->expects($this->exactly(2))
            ->method('delete')
            ->willReturnCallback(function (string $key): bool {
                self::assertContains(
                    $key,
                    ['available_products', 'available_product_types'],
                );

                return true;
            });

        $service = new ProductCatalogService($cache, $repository);

        $service->invalidateCatalogCache();
    }
}
