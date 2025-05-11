<?php

use DI\Container;
use Slim\Factory\AppFactory;
use App\StatsService;
use Dotenv\Dotenv;

require __DIR__ . '/vendor/autoload.php';

$container = new Container();

// Загрузка .env
//if (file_exists(__DIR__ . '/.env')) {
//    $dotenv = Dotenv::createImmutable(__DIR__);
//    $dotenv->load();
//}

// Настройка Redis в контейнере
$container->set(\Redis::class, function () {
    $redis = new \Redis();
    $host = $_ENV['REDIS_HOST'] ?? 'localhost';
    $port = (int)($_ENV['REDIS_PORT'] ?? 6379);
    $db = (int)($_ENV['REDIS_DB'] ?? 0);
    $redis->connect($host, $port);
    $redis->select($db);
    return $redis;
});

AppFactory::setContainer($container);
$app = AppFactory::create();

// Подключение роутов
(require __DIR__ . '/routes.php')($app);

return $app;