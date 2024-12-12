<?php

namespace App\Service\Misc;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;

class CacheHandler
{
    private FilesystemAdapter $cache;
    public function __construct()
    {
        $this->cache = new FilesystemAdapter();
    }

    public function getItemOrUpdate(string $key, mixed $data): CacheItem
    {
        $item = $this->cache->getItem($key);

        if (!$item->isHit()) {
            if (is_callable($data)) {
                $item->set($data());
            } else {
                $item->set($data);
            }

            $this->cache->save($item);
        }

        return $item;
    }

    public function updateItem(string $key, mixed $data): void
    {
        $item = $this->cache->getItem($key);
        $item->set($data);
        $this->cache->save($item);
    }

    public function getItem(string $key): mixed
    {
        return $this->cache->getItem($key)->get();
    }
}