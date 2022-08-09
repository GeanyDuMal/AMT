<?php

namespace App\Manager;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class PictureUtils
{
    public EntityManagerInterface $manager;

    public function downloadPicture(string $link, string $location): string
    {
        $locationUsed = $location;

        if ($locationUsed[0] == "/") {
            $locationUsed = substr($locationUsed, 1);
        }

        file_put_contents($locationUsed ,file_get_contents($link));

        return $location;
    }
}