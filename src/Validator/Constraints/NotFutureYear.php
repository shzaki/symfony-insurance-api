<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class NotFutureYear extends Constraint
{
    public string $message = 'The year {{ value }} must not be in the future.';
}
