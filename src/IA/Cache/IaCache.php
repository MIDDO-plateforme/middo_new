<?php

namespace App\IA\Cache;

use Symfony\Contracts\Cache\CacheInterface;

class IaCache
{
    public function __construct(private CacheInterface $cache)
    {
    }

    public function get(string $prompt): ?string
    {
        $item = $this->cache->getItem('ia_'.md5($prompt));

        if (!$item->isHit()) {
            return null;
        }

        return $item->get();
    }

    public function set(string $prompt, string $answer): void
    {
        $item = $this->cache->getItem('ia_'.md5($prompt));
        $item->set($answer);
        $item->expiresAfter(3600); // 1h
        $this->cache->save($item);
    }
}
