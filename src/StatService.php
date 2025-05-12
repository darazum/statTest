<?php

namespace App;

class StatService
{
    private \Redis $redis;
    private CountryListService $countryListService;
    private const HASH_KEY = 'visits';

    public function __construct(\Redis $redis, CountryListService $countryListService)
    {
        $this->redis = $redis;
        $this->countryListService = $countryListService;
    }

    private function countryKey(string $country): string
    {
        $country = strtoupper($country);
        return self::HASH_KEY . "{$country}";
    }

    public function increment(string $country): void
    {
        $t = microtime(true);
        $this->redis->incr($this->countryKey($country));
        // TODO: debug
//        $tRedis = microtime(true) - $t;
//        if (mt_rand(0, 1000) == 0) {
//            error_log('redis incr in ' . round($tRedis, 4) . ' seconds');
//        }
//        if ($tRedis > 0.01) {
//            error_log('SLOW redis incr in ' . round($tRedis, 4) . ' seconds');
//        }
    }

    public function get(string $country): string
    {
        return $this->redis->get($this->countryKey($country)) ?? 0;
    }

    public function getAll(): array
    {
        $countries = $this->countryListService->getCountries();
        $keys = array_map(fn($country) => $this->countryKey($country), $countries);
        $values = $this->redis->mGet($keys);

        $result = [];
        $total = 0;
        foreach ($countries as $i => $country) {
            $value = (int)($values[$i] ?? 0);
            $total += $value;
            $result[$country] = $value;
        }

        // не нужно но оч. удобно дебажить после бенчей
        arsort($result);

        return ['total' => $total, 'counters' => $result];
    }
}