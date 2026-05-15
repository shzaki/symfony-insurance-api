<?php

namespace App\Controller\Comparison;

use App\Application\Comparison\Dto\Request\ComparisonRequestDto;
use App\Application\Comparison\Service\ComparisonService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ComparisonController extends AbstractController
{
    #[Route('/api/comparisons', name: 'api_comparisons_create', methods: ['POST'])]
    public function create(
        Request $request,
        ComparisonService $comparisonService,
        ValidatorInterface $validator,
    ): JsonResponse {
        $data = $request->toArray();

        $comparisonRequestDto = ComparisonRequestDto::fromArray($data);

        $violations = $validator->validate($comparisonRequestDto);

        if (count($violations) > 0) {
            return new JsonResponse([
                'errors' => array_map(
                    static fn($v) => [
                        'field' => $v->getPropertyPath(),
                        'message' => $v->getMessage(),
                    ],
                    iterator_to_array($violations),
                ),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $comparisonResponseDto = $comparisonService->compare($comparisonRequestDto);

        return new JsonResponse($comparisonResponseDto->toArray(), JsonResponse::HTTP_CREATED);
    }
}
