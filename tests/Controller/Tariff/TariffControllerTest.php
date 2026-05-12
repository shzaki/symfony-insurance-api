<?php

namespace App\Tests\Controller\Tariff;

use App\Application\Tariff\Dto\Request\BestTariffsRequestDto;
use App\Application\Tariff\Service\TariffRecommendationService;
use App\Controller\Tariff\TariffController;
use App\Entity\InsuranceProduct;
use App\Entity\InsuranceProvider;
use App\Entity\Tariff;
use App\Repository\TariffRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class TariffControllerTest extends TestCase
{
    public function testBestTariffsReturnsSuccessfulJsonResponse(): void
    {
        $tariff = self::createTariff();

        $tariffRepository = $this->createMock(TariffRepository::class);
        $tariffRepository
            ->expects($this->once())
            ->method('findBestActiveTariffs')
            ->with('building', null, 3)
            ->willReturn([$tariff]);

        $service = new TariffRecommendationService($tariffRepository);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->expects($this->once())
            ->method('validate')
            ->with(self::isInstanceOf(BestTariffsRequestDto::class))
            ->willReturn(new ConstraintViolationList());

        $controller = new TariffController();
        $request = Request::create('/api/tariffs/best?limit=3', 'GET');

        $response = $controller->best($request, $service, $validator);

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());

        $data = json_decode((string) $response->getContent(), true);

        self::assertIsArray($data);
        self::assertCount(1, $data);
        self::assertSame('Premium', $data[0]['name']);
        self::assertSame('39.90', $data[0]['monthlyPrice']);
        self::assertSame('Allianz', $data[0]['providerName']);
        self::assertSame('Premium Building Insurance', $data[0]['productName']);
    }

    private static function createTariff(): Tariff
    {
        $provider = new InsuranceProvider();
        $provider->setName('Allianz');

        $product = new InsuranceProduct();
        $product->setName('Premium Building Insurance');
        $product->setProvider($provider);

        $tariff = new Tariff();
        self::setEntityId($tariff, 1);
        $tariff->setName('Premium');
        $tariff->setMonthlyPrice('39.90');
        $tariff->setCoverageAmount(1000000);
        $tariff->setDeductible(250);
        $tariff->setScore(95);
        $tariff->setProduct($product);

        return $tariff;
    }

    private static function setEntityId(object $entity, int $id): void
    {
        $reflectionProperty = new \ReflectionProperty($entity, 'id');
        $reflectionProperty->setValue($entity, $id);
    }
}
