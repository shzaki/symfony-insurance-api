<?php

namespace App\Controller\Tariff;

use App\Application\Tariff\Service\TariffQueryService;
use App\Application\Tariff\Dto\Request\TariffQueryRequestDto;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class TariffController extends AbstractController
{
    #[Route('/api/tariffs', name: 'api_tariffs_list', methods: ['GET'])]
    public function list(
        Request            $request,
        TariffQueryService $tariffQueryService,
        ValidatorInterface $validator,
    ): JsonResponse {
        $tariffRequestDto = TariffQueryRequestDto::fromQuery($request->query->all());

        $violations = $validator->validate($tariffRequestDto);

        if (count($violations) > 0) {
            return new JsonResponse([
                'errors' => array_map(
                    static fn($violation) => [
                        'field' => $violation->getPropertyPath(),
                        'message' => $violation->getMessage(),
                    ],
                    iterator_to_array($violations),
                ),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $tariffResponseDtos = $tariffQueryService->getTariffs($tariffRequestDto);

        return new JsonResponse($tariffResponseDtos);
    }
}
