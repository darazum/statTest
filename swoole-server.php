<?php

use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;

$app = require __DIR__ . '/bootstrap.php';

$server = new Swoole\Http\Server("0.0.0.0", 9501);

$server->on("request", function ($req, $res) use ($app) {
    // Преобразуем Swoole Request → PSR-7 Request
    $psrRequest = (new ServerRequestFactory())->createServerRequest(
        $req->server['request_method'],
        $req->server['request_uri']
    );
    $psrRequest = $psrRequest->withParsedBody($req->post ?? []);

    // Обработка запроса через Slim
    $response = $app->handle($psrRequest);

    // Переносим ответ в Swoole Response
    foreach ($response->getHeaders() as $name => $values) {
        foreach ($values as $value) {
            $res->header($name, $value);
        }
    }

    $res->status($response->getStatusCode());
    $res->end((string)$response->getBody());
});

$server->start();