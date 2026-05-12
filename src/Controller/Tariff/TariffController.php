<?php

namespace App\Controller\Tariff;

use App\Application\Tariff\Dto\Response\TariffResponseDto;
use App\Application\Tariff\Service\TariffRecommendationService;
use App\Application\Tariff\Dto\Request\BestTariffsRequestDto;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class TariffController extends AbstractController
{
    #[Route('/api/tariffs/best', name: 'api_tariffs_best', methods: ['GET'])]
    public function best(
        Request $request,
        TariffRecommendationService $tariffRecommendationService,
        ValidatorInterface $validator,
    ): JsonResponse {
        $requestDto = BestTariffsRequestDto::fromQuery($request->query->all());

        $violations = $validator->validate($requestDto);

        if (count($violations) > 0) {
            return $this->json([
                'errors' => array_map(
                    static fn($violation) => [
                        'field' => $violation->getPropertyPath(),
                        'message' => $violation->getMessage(),
                    ],
                    iterator_to_array($violations),
                ),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $tariffs = $tariffRecommendationService->findBestTariffs($requestDto);

        $data = array_map(
            static fn($tariff) => TariffResponseDto::fromEntity($tariff),
            $tariffs,
        );

        return new JsonResponse($data);
    }
}
