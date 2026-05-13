<?php

namespace App\Tests\EventSubscriber;

use App\EventSubscriber\ApiExceptionSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class ApiExceptionSubscriberTest extends TestCase
{
    public function testItReturnsHttpExceptionStatusCode(): void
    {
        $subscriber = new ApiExceptionSubscriber();

        $kernel = $this->createMock(HttpKernelInterface::class);

        $event = new ExceptionEvent(
            $kernel,
            Request::create('/api/tariffs'),
            HttpKernelInterface::MAIN_REQUEST,
            new NotFoundHttpException('Tariff not found'),
        );

        $subscriber->onKernelException($event);

        $response = $event->getResponse();

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());

        $data = json_decode((string) $response->getContent(), true);

        self::assertSame('Tariff not found', $data['error']['message']);
        self::assertSame(NotFoundHttpException::class, $data['error']['type']);
    }

    public function testItReturnsInternalServerErrorForGenericExceptions(): void
    {
        $subscriber = new ApiExceptionSubscriber();

        $kernel = $this->createMock(HttpKernelInterface::class);

        $event = new ExceptionEvent(
            $kernel,
            Request::create('/api/tariffs'),
            HttpKernelInterface::MAIN_REQUEST,
            new \RuntimeException('Unexpected failure'),
        );

        $subscriber->onKernelException($event);

        $response = $event->getResponse();

        self::assertInstanceOf(JsonResponse::class, $response);
        self::assertSame(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());

        $data = json_decode((string) $response->getContent(), true);

        self::assertSame('Unexpected failure', $data['error']['message']);
        self::assertSame(\RuntimeException::class, $data['error']['type']);
    }
}
