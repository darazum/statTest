<?php

namespace App;

use Slim\Psr7\Factory\ServerRequestFactory;

class SwooleRequestBridge
{
    public static function fromSwoole(\Swoole\Http\Request $req): \Psr\Http\Message\ServerRequestInterface
    {
        $request = (new ServerRequestFactory())->createServerRequest(
            $req->server['request_method'] ?? 'GET',
            $req->server['request_uri'] ?? '/'
        );

        if (!empty($req->get)) {
            $request = $request->withQueryParams($req->get);
        }

        return $request;
    }
}