<?php

declare(strict_types=1);

use Peso\Core\Services\ChainService;
use Peso\Core\Services\IndirectExchangeService;
use Peso\Core\Services\PesoServiceInterface;
use Peso\Core\Services\ReversibleService;
use Peso\Core\Services\TrivialService;
use Peso\Peso\CurrencyConverter;
use Peso\Peso\Options\ConversionType;
use Peso\Services\CzechNationalBank\CentralBankFixingService;
use Peso\Services\CzechNationalBank\OtherCurrenciesService;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

use function DI\autowire;

return [
    // peso
    PesoServiceInterface::class => function (CacheInterface $cache) {
        return new ChainService(
            new TrivialService(),
            new IndirectExchangeService(new ReversibleService(new ChainService(
                new CentralBankFixingService($cache),
                new OtherCurrenciesService($cache)
            )), 'CZK'),
        );
    },
    CurrencyConverter::class => autowire()
        ->constructorParameter('conversionType', ConversionType::CalculatedOnly)
    ,

    // cache
    CacheInterface::class => function () {
        return new Psr16Cache(new FilesystemAdapter(directory: __DIR__ . '/../../var/cache/symfony'));
    },
];
