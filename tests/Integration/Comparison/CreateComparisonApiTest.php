<?php

namespace App\Tests\Integration\Comparison;

use App\Entity\InsuranceProduct;
use App\Entity\InsuranceProvider;
use App\Entity\Tariff;
use App\Tests\Integration\IntegrationTestCase;

final class CreateComparisonApiTest extends IntegrationTestCase
{
    public function testItCreatesComparisonSuccessfully(): void
    {
        $this->createTariff(
            providerName: 'Allianz',
            providerCode: 'allianz',
            tariffName: 'Premium',
            monthlyPrice: '39.50',
            score: 96,
        );

        $client = static::getClient();

        $client->request(
            'POST',
            '/api/comparisons',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'zipcode' => '80331',
                'building_year' => 2018,
                'living_area' => 120,
                'building_type' => 'house',
                'has_garage' => true,
                'has_solar_panels' => false,
            ], JSON_THROW_ON_ERROR),
        );

        self::assertResponseStatusCodeSame(201);

        $data = json_decode(
            (string) $client->getResponse()->getContent(),
            true,
        );

        self::assertArrayHasKey('comparisonId', $data);
        self::assertSame(1, $data['totalResults']);
        self::assertCount(1, $data['results']);
        self::assertSame('Premium', $data['results'][0]['tariffName']);
        self::assertSame('Allianz', $data['results'][0]['providerName']);
        self::assertSame('39.50', $data['results'][0]['monthlyPrice']);
    }

    public function testItReturnsBadRequestForInvalidPayload(): void
    {
        $client = static::getClient();

        $client->request(
            'POST',
            '/api/comparisons',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'zipcode' => '',
                'building_year' => 1700,
                'living_area' => 0,
                'building_type' => '',
            ], JSON_THROW_ON_ERROR),
        );

        self::assertResponseStatusCodeSame(400);

        $data = json_decode(
            (string) $client->getResponse()->getContent(),
            true,
        );

        self::assertArrayHasKey('errors', $data);
        self::assertNotEmpty($data['errors']);
    }

    private function createTariff(
        string $providerName,
        string $providerCode,
        string $tariffName,
        string $monthlyPrice,
        int $score,
    ): void {
        $provider = new InsuranceProvider();
        $provider->setName($providerName);
        $provider->setCode($providerCode);
        $provider->setIsActive(true);

        $product = new InsuranceProduct();
        $product->setName('Building Insurance');
        $product->setType('building');
        $product->setDescription('Protects residential buildings');
        $product->setProvider($provider);
        $product->setIsActive(true);

        $tariff = new Tariff();
        $tariff->setName($tariffName);
        $tariff->setMonthlyPrice($monthlyPrice);
        $tariff->setCoverageAmount(1000000);
        $tariff->setDeductible(250);
        $tariff->setScore($score);
        $tariff->setProduct($product);
        $tariff->setIsActive(true);

        $this->entityManager->persist($provider);
        $this->entityManager->persist($product);
        $this->entityManager->persist($tariff);
        $this->entityManager->flush();
    }
}
