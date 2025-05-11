<?php

namespace App;

class StatsService
{
    private \Redis $redis;
    private const HASH_KEY = 'visits';

    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    public function increment(string $country): void
    {
        $this->redis->hIncrBy(self::HASH_KEY, strtolower($country), 1);
    }

    public function getAll(): array
    {
        return $this->redis->hGetAll(self::HASH_KEY);
    }
}