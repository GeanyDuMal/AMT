<?php

namespace App\Utils\Enum;

use ReflectionClass;

abstract class AbstractEnumClass
{
    public static function getAll(): array
    {
        // static fait reference à la classe qui appelle cette function
        $reflectionClass = new ReflectionClass(static::class);
        return $reflectionClass->getConstants();
    }
}