<?php

namespace App\Utils;

class RandomUtils
{

    public function randomString(int $size, string $characters): string {
        $string = "";

        for ($i = 0; $i < $size; $i++) {
            $string = $string . $characters[rand(0, strlen($characters)-1)];
        }

        return $string;
    }
}