<?php

namespace App\Tests\Integration\Product;

use App\Entity\InsuranceProduct;
use App\Entity\InsuranceProvider;
use App\Tests\Integration\IntegrationTestCase;

final class ProductTypesApiTest extends IntegrationTestCase
{
    public function testItReturnsAvailableProductTypes(): void
    {
        $provider = new InsuranceProvider();
        $provider->setName('Allianz');
        $provider->setCode('allianz');

        $product = new InsuranceProduct();
        $product->setName('Building Insurance');
        $product->setType('building');
        $product->setDescription('Protects residential buildings');
        $product->setProvider($provider);
        $product->setIsActive(true);

        $this->entityManager->persist($provider);
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $client = static::getClient();
        $client->request('GET', '/api/products/types');

        self::assertResponseIsSuccessful();

        $data = json_decode(
            (string) $client->getResponse()->getContent(),
            true,
        );

        self::assertIsArray($data);
        self::assertContains('building', $data);
    }
}
