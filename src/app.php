<?php

declare(strict_types=1);

use DI\Bridge\Slim\Bridge;
use Peso\Demo\ServiceController;
use Slim\Routing\RouteCollectorProxy;

$container = require __DIR__ . '/container.php';
$app = Bridge::create($container);

$app->group('/service.php', function (RouteCollectorProxy $service) {
    $service->get('/convert', [ServiceController::class, 'convert']);
    $service->get('/currencies', [ServiceController::class, 'currencies']);
});

$app->addErrorMiddleware(getenv('DEBUG') === '1', false, false);

return $app;
