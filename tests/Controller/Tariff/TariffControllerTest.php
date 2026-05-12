<?php

namespace App\Tests\Controller\Tariff;

use App\Application\Tariff\Dto\Request\TariffQueryRequestDto;
use App\Application\Tariff\Service\TariffQueryService;
use App\Controller\Tariff\TariffController;
use App\Entity\InsuranceProduct;
use App\Entity\InsuranceProvider;
use App\Entity\Tariff;
use App\Repository\TariffRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class TariffControllerTest extends TestCase
{
    public function testBestTariffsReturnsSuccessfulJsonResponse(): void
    {
        $tariff = self::createTariff();

        $tariffRepository = $this->createMock(TariffRepository::class);
        $tariffRepository
            ->expects($this->once())
            ->method('findActiveTariffs')
            ->with('building', null, 3, 1, 'score', 'desc')
            ->willReturn([$tariff]);

        $service = new TariffQueryService($tariffRepository);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->expects($this->once())
            ->method('validate')
            ->with(self::isInstanceOf(TariffQueryRequestDto::class))
            ->willReturn(new ConstraintViolationList());

        $controller = new TariffController();
        $request = Request::create('/api/tariffs?limit=3', 'GET');

        $response = $controller->list($request, $service, $validator);

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

    public function testBestTariffsPassesFiltersPaginationAndSortingToRepository(): void
    {
        $tariff = self::createTariff();

        $tariffRepository = $this->createMock(TariffRepository::class);
        $tariffRepository
            ->expects($this->once())
            ->method('findActiveTariffs')
            ->with('building', 'AXA', 5, 2, 'price', 'asc')
            ->willReturn([$tariff]);

        $service = new TariffQueryService($tariffRepository);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->expects($this->once())
            ->method('validate')
            ->with(self::isInstanceOf(TariffQueryRequestDto::class))
            ->willReturn(new ConstraintViolationList());

        $controller = new TariffController();
        $request = Request::create(
            '/api/tariffs?limit=5&page=2&sort=price&direction=asc&provider=AXA&product_type=building',
            'GET',
        );

        $response = $controller->list($request, $service, $validator);

        self::assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    public function testBestTariffsReturnsBadRequestWhenValidationFails(): void
    {
        $tariffRepository = $this->createMock(TariffRepository::class);
        $tariffRepository
            ->expects($this->never())
            ->method('findActiveTariffs');

        $service = new TariffQueryService($tariffRepository);

        $violations = new ConstraintViolationList([
            new ConstraintViolation(
                message: 'This value should be positive.',
                messageTemplate: null,
                parameters: [],
                root: null,
                propertyPath: 'limit',
                invalidValue: 0,
            ),
        ]);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->expects($this->once())
            ->method('validate')
            ->with(self::isInstanceOf(TariffQueryRequestDto::class))
            ->willReturn($violations);

        $controller = new TariffController();
        $request = Request::create('/api/tariffs?limit=abc', 'GET');

        $response = $controller->list($request, $service, $validator);

        self::assertSame(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());

        $data = json_decode((string) $response->getContent(), true);

        self::assertSame([
            'errors' => [
                [
                    'field' => 'limit',
                    'message' => 'This value should be positive.',
                ],
            ],
        ], $data);
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
