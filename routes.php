<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use App\StatService;

return function (App $app) {
    $app->get('/stats', function (Request $request, Response $response) use ($app) {
        $statService = $app->getContainer()->get(StatService::class);
        $data = $statService->getAll();
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->get('/visit', function (Request $request, Response $response) use ($app) {

        try {
            $statService = $app->getContainer()->get(StatService::class);
            $params = (array)$request->getQueryParams();
            $country = $params['country'] ?? null;

            if (!$country) {
                $response->getBody()->write("Missing 'country'");
                return $response->withStatus(400);
            }

            $statService->increment($country);
            $response->getBody()->write("OK");

            return $response;
        } catch (Exception $e) {
            error_log("[EXCEPTION] " . $e->getMessage());
            $response->getBody()->write("Internal error");
            return $response->withStatus(500);
        }
    });


    $app->map(['GET', 'POST', 'PUT', 'DELETE'], '/{routes:.+}', function ($request, $response) {
        $response->getBody()->write('Not Found');
        return $response->withStatus(404);
    });
};