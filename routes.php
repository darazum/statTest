<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use App\StatsService;

return function (App $app) {
    $container = $app->getContainer();
    $statsService = $container->get(StatsService::class);

    $app->post('/visit', function (Request $request, Response $response) use ($statsService) {
        $params = (array)$request->getParsedBody();
        $country = $params['country'] ?? null;

        if (!$country) {
            $response->getBody()->write("Missing 'country'");
            return $response->withStatus(400);
        }

        $statsService->increment($country);
        $response->getBody()->write("OK");
        return $response;
    });

    $app->get('/stats', function (Request $request, Response $response) use ($statsService) {
        $data = $statsService->getAll();
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    });
};