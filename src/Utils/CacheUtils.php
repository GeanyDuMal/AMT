<?php

namespace App\Utils;

use App\Entity\Client;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CacheUtils {
    private FilesystemAdapter $cache;

    public function __construct() {
        $this->cache = new FilesystemAdapter();
    }

    /**
     * @param Object $object object to store
     * @param string $key key where the object is stored
     * @return void
     * @throws InvalidArgumentException
     */
    public function saveInCache(object $object, string $key): void {
        $itemToSave = $this->cache->getItem($key);

        $itemToSave->expiresAfter(1800);
        $itemToSave->set($object);

        $this->cache->save($itemToSave);
    }

    /**
     * @param string $key key where the object will be searched
     * @return mixed Data stored at "key" in cache
     * @throws InvalidArgumentException
     */
    public function getFromCache(string $key): mixed {
        $itemToGet = $this->cache->getItem($key);

        if ($itemToGet->isHit()) {
            return $itemToGet->get();
        } else {
            return null;
        }
    }

    /**
     * @param string $key
     * @return void
     * @throws InvalidArgumentException
     */
    public function deleteFromCache(string $key): void {
        $this->cache->deleteItem($key);
    }
}