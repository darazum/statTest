<?php

use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use App\SwooleRequestBridge;

$app = require __DIR__ . '/bootstrap.php';

$server = new Server("0.0.0.0", 9501);

$server->on("start", function () {
    echo "Swoole HTTP server started at http://0.0.0.0:9501\n";
});

$server->on("request", function (Request $req, Response $res) use ($app) {
    try {
        $psrRequest = SwooleRequestBridge::fromSwoole($req);
        $psrResponse = $app->handle($psrRequest);

        $res->status($psrResponse->getStatusCode());

        foreach ($psrResponse->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $res->header($name, $value);
            }
        }

        $res->end((string)$psrResponse->getBody());
    } catch (\Throwable $e) {
        error_log('[ERROR] ' . $e->getMessage());
        $res->status(500);
        $res->end("Internal Server Error");
    }
});

$server->start();