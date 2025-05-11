<?php

use DI\Container;
use Slim\Factory\AppFactory;
use App\StatService;

require __DIR__ . '/vendor/autoload.php';

$container = new Container();

// Явно регистрируем Redis
$container->set(Redis::class, function () {
    $redis = new Redis();
    $redis->connect($_ENV['REDIS_HOST'], (int)$_ENV['REDIS_PORT']);
    $redis->select((int)($_ENV['REDIS_DB'] ?? 0));
    return $redis;
});

// Явно регистрируем StatService с Redis как зависимостью
$container->set(StatService::class, function ($c) {
    return new StatService($c->get(Redis::class));
});

AppFactory::setContainer($container);
$app = AppFactory::create();

// Подключаем роуты
(require __DIR__ . '/routes.php')($app);

return $app;