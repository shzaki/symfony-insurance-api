<?php

namespace App\Controller\Product;

use App\Application\Product\Service\ProductCatalogService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'api_products', methods: ['GET'])]
    public function list(ProductCatalogService $productCatalogService): JsonResponse
    {
        return new JsonResponse($productCatalogService->getProducts());
    }

    #[Route('/api/products/types', name: 'api_products_types', methods: ['GET'])]
    public function types(ProductCatalogService $productCatalogService): JsonResponse
    {
        return new JsonResponse($productCatalogService->getProductTypes());
    }
}
