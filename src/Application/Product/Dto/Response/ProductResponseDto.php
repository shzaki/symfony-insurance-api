<?php

namespace App\Application\Product\Dto\Response;

use App\Entity\InsuranceProduct;

final readonly class ProductResponseDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $providerName,
    ) {
    }

    public static function fromEntity(InsuranceProduct $product): self
    {
        return new self(
            id: $product->getId(),
            name: $product->getName(),
            providerName: $product->getProvider()->getName(),
        );
    }
}
