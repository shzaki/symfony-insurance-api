<?php

namespace App\Application\Tariff\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TariffNotFoundException extends NotFoundHttpException
{
    public function __construct(int $tariffId)
    {
        parent::__construct(sprintf('Tariff with id %d was not found.', $tariffId));
    }
}
