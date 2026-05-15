<?php

namespace App\Tests\Integration\Tariff;

use App\Entity\InsuranceProduct;
use App\Entity\InsuranceProvider;
use App\Entity\Tariff;
use App\Tests\Integration\IntegrationTestCase;

final class GetTariffApiTest extends IntegrationTestCase
{
    public function testItReturnsTariffSuccessfully(): void
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

        $tariff = new Tariff();
        $tariff->setName('Premium');
        $tariff->setMonthlyPrice('39.50');
        $tariff->setCoverageAmount(1000000);
        $tariff->setDeductible(250);
        $tariff->setScore(96);
        $tariff->setProduct($product);
        $tariff->setIsActive(true);

        $this->entityManager->persist($provider);
        $this->entityManager->persist($product);
        $this->entityManager->persist($tariff);
        $this->entityManager->flush();

        $client = static::getClient();

        $client->request('GET', '/api/tariffs/' . $tariff->getId());

        self::assertResponseIsSuccessful();

        $data = json_decode(
            (string) $client->getResponse()->getContent(),
            true,
        );

        self::assertSame('Premium', $data['name']);
        self::assertSame('39.50', $data['monthlyPrice']);
        self::assertSame('Allianz', $data['providerName']);
        self::assertSame('Building Insurance', $data['productName']);
    }

    public function testItReturnsNotFoundForUnknownTariff(): void
    {
        $client = static::getClient();

        $client->request('GET', '/api/tariffs/999999');

        self::assertResponseStatusCodeSame(404);

        $data = json_decode(
            (string) $client->getResponse()->getContent(),
            true,
        );

        self::assertSame(
            'Tariff with id 999999 was not found.',
            $data['error']['message'],
        );
    }
}
