<?php

declare(strict_types=1);

use DI\ContainerBuilder;

require __DIR__ . '/../vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->addDefinitions(
    __DIR__ . '/container/services.php',
);
$builder->enableCompilation(__DIR__ . '/../var/cache/di');
$container = $builder->build();

return $container;
