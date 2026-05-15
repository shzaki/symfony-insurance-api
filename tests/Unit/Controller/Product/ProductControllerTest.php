<?php

namespace App\Tests\Unit\Controller\Product;

use App\Application\Product\Dto\Response\ProductResponseDto;
use App\Application\Product\Service\ProductCatalogService;
use App\Controller\Product\ProductController;
use App\Repository\InsuranceProductRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\CacheInterface;

final class ProductControllerTest extends TestCase
{
    public function testProductsReturnsSuccessfulJsonResponse(): void
    {
        $insuranceProductRepository = $this->createStub(InsuranceProductRepository::class);
        $cache = $this->createMock(CacheInterface::class);
        $cache
            ->expects($this->once())
            ->method('get')
            ->with('available_products')
            ->willReturn([
                new ProductResponseDto(
                    id: 1,
                    name: 'Building Insurance',
                    providerName: 'Allianz',
                ),
            ]);

        $productCatalogService = new ProductCatalogService(
            $cache,
            $insuranceProductRepository,
        );

        $controller = new ProductController();

        $response = $controller->list($productCatalogService);

        self::assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());

        $data = json_decode((string) $response->getContent(), true);

        self::assertCount(1, $data);
        self::assertSame('Building Insurance', $data[0]['name']);
    }

    public function testProductTypesReturnsSuccessfulJsonResponse(): void
    {
        $insuranceProductRepository = $this->createStub(InsuranceProductRepository::class);
        $cache = $this->createMock(CacheInterface::class);
        $cache
            ->expects($this->once())
            ->method('get')
            ->with('available_product_types')
            ->willReturn([
                'building',
                'vehicle',
            ]);

        $productCatalogService = new ProductCatalogService(
            $cache,
            $insuranceProductRepository,
        );

        $controller = new ProductController();

        $response = $controller->types($productCatalogService);

        self::assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());

        $data = json_decode((string) $response->getContent(), true);

        self::assertSame([
            'building',
            'vehicle',
        ], $data);
    }
}
