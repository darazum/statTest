<?php
$country = $_GET['country'] ?? null;

if (!$country || !preg_match('/^[A-Z]{2}$/', strtoupper($country))) {
    http_response_code(400);
    echo "Invalid or missing country param";
    exit;
}

$redis = new Redis();
$redis->pconnect(getenv('REDIS_HOST') ?: 'redis', 6379);
$key = 'visits' . strtoupper($country);
$redis->incr($key);
echo "OKKK";