<?php

namespace App\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Exception;

class PictureUtils
{
    public EntityManagerInterface $manager;

    /**
     * @param string $link The link of the picture
     * @param string $location The location where the picture will be saved
     * @return string The location of the picture
     */
    public function downloadPicture(string $link, string $location): string
    {
        $locationUsed = $this->adaptLocation($location);

        try {
            file_put_contents($locationUsed ,file_get_contents($link));
        } catch (Exception $e){
            $location = "/img/entity/placeholder.png";
        }

        return $location;
    }

    /**
     * @param string $location The location of the picture which is about to be deleted
     * @return void
     */
    public function deletePicture(string $location): void
    {
        $locationUsed = $this->adaptLocation($location);

        try {
            if (fopen($locationUsed, 'rw') && !str_ends_with($locationUsed, 'placeholder.png')){
                unlink($locationUsed);
            }
        } catch (Exception $e){
        }
    }

    /**
     * Adapt the location to download or remove the file.
     * The link need to be adapted because there is a '/' before to directly use it html pages
     * @param string $location
     * @return string
     */
    public function adaptLocation(string $location): string
    {
        if ($location[0] == "/") {
            $location = substr($location, 1);
        }

        return $location;
    }
}