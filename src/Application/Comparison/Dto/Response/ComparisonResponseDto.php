<?php

namespace App\Application\Comparison\Dto\Response;

final readonly class ComparisonResponseDto
{
    /**
     * @param ComparisonResultDto[] $results
     */
    public function __construct(
        public int   $comparisonId,
        public int   $totalComparisonResults,
        public array $results,
    ) {
    }

    public function toArray(): array
    {
        return [
            'comparisonId' => $this->comparisonId,
            'totalResults' => $this->totalComparisonResults,
            'results' => array_map(
                static fn(ComparisonResultDto $comparisonResultDto) => $comparisonResultDto->toArray(),
                $this->results,
            ),
        ];
    }
}
