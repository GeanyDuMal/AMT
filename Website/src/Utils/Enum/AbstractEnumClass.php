<?php

namespace App\Utils\Enum;

abstract class AbstractEnumClass
{
    public function getAll(): array{
        return get_class_vars(self::class);
    }
}