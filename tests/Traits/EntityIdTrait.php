<?php

namespace App\Tests\Traits;

use ReflectionException;
use ReflectionProperty;

trait EntityIdTrait
{
    /**
     * @throws ReflectionException
     */
    private static function setEntityId(object $entity, int $id): void
    {
        $reflectionProperty = new ReflectionProperty($entity, 'id');
        $reflectionProperty->setValue($entity, $id);
    }
}
