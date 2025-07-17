<?php

declare(strict_types=1);

use DI\Bridge\Slim\Bridge;

$container = require __DIR__ . '/container.php';
$app = Bridge::create($container);

$app->addErrorMiddleware(false, false, false);

return $app;
