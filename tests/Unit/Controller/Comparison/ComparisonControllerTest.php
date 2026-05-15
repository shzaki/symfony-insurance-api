<?php

namespace App\Tests\Unit\Controller\Comparison;

use App\Application\Comparison\Service\ComparisonService;
use App\Controller\Comparison\ComparisonController;
use App\Repository\TariffRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ComparisonControllerTest extends TestCase
{
    public function testCreateReturnsSuccessfulJsonResponse(): void
    {
        $request = new Request(
            content: json_encode([
                'zipcode' => '80331',
                'building_year' => 2018,
                'living_area' => 120,
                'building_type' => 'house',
                'has_garage' => true,
                'has_solar_panels' => false,
            ], JSON_THROW_ON_ERROR),
        );

        $tariffRepository = $this->createMock(TariffRepository::class);
        $tariffRepository
            ->expects($this->once())
            ->method('findActiveTariffs')
            ->willReturn([]);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->once())
            ->method('wrapInTransaction')
            ->willReturnCallback(static function (callable $callback): void {
                $callback();
            });

        $entityManager
            ->expects($this->once())
            ->method('persist');

        $entityManager
            ->expects($this->once())
            ->method('flush');

        $comparisonService = new ComparisonService(
            $tariffRepository,
            $entityManager,
        );

        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $controller = new ComparisonController();

        $response = $controller->create(
            $request,
            $comparisonService,
            $validator,
        );

        self::assertSame(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $data = json_decode((string)$response->getContent(), true);

        self::assertArrayHasKey('comparisonId', $data);
        self::assertSame(0, $data['totalResults']);
        self::assertSame([], $data['results']);
    }

    public function testCreateReturnsBadRequestWhenValidationFails(): void
    {
        $request = new Request(
            content: json_encode([
                'zipcode' => '',
                'building_year' => 1700,
            ], JSON_THROW_ON_ERROR),
        );

        $tariffRepository = $this->createStub(TariffRepository::class);

        $entityManager = $this->createStub(EntityManagerInterface::class);

        $comparisonService = new ComparisonService(
            $tariffRepository,
            $entityManager,
        );

        $violation = $this->createStub(\Symfony\Component\Validator\ConstraintViolationInterface::class);

        $violation
            ->method('getPropertyPath')
            ->willReturn('zipcode');

        $violation
            ->method('getMessage')
            ->willReturn('This value should not be blank.');

        $violations = new ConstraintViolationList([$violation]);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->expects($this->once())
            ->method('validate')
            ->willReturn($violations);

        $controller = new ComparisonController();

        $response = $controller->create(
            $request,
            $comparisonService,
            $validator,
        );

        self::assertSame(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());

        $data = json_decode((string)$response->getContent(), true);

        self::assertArrayHasKey('errors', $data);
        self::assertCount(1, $data['errors']);
        self::assertSame('zipcode', $data['errors'][0]['field']);
        self::assertSame('This value should not be blank.', $data['errors'][0]['message']);
    }
}

