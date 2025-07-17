<?php

declare(strict_types=1);

use DI\ContainerBuilder;

require __DIR__ . '/../vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->enableCompilation(__DIR__ . '/../var/cache/di');
$container = $builder->build();

return $container;
