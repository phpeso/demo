<?php

declare(strict_types=1);

namespace Peso\Demo;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Peso\Peso\CurrencyConverter;
use Psr\Http\Message\ResponseInterface;
use Punic\Currency as IntlCurrency;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

final readonly class ServiceController
{
    public function convert(ServerRequest $request, Response $response, CurrencyConverter $peso): ResponseInterface
    {
        $from = $request->getQueryParam('from', '');
        $to = $request->getQueryParam('to', '');
        $amount = $request->getQueryParam('amount', '');

        $isoCourrencies = new ISOCurrencies();

        if (!$isoCourrencies->contains(new Currency($from))) {
            return $response->withJson(['message' => 'Invalid base currency'], 422);
        }
        if (!$isoCourrencies->contains(new Currency($to))) {
            return $response->withJson(['message' => 'Invalid target currency'], 422);
        }
        if (!is_numeric($amount)) {
            return $response->withJson(['message' => 'Invalid amount'], 422);
        }

        $precision = $isoCourrencies->subunitFor(new Currency($to));

        $converted = $peso->convert($amount, $from, $to, $precision + 4);

        return $response->withJson([
            'amount' => substr($converted, 0, -4),
            'tail' => substr($converted, -4),
        ]);
    }

    public function currencies(Response $response): ResponseInterface
    {
        $currencies = [
            'EUR', 'USD', 'JPY', 'BGN', 'CZK', 'DKK', 'GBP', 'HUF', 'PLN', 'RON', 'SEK', 'CHF', 'ISK', 'NOK', 'TRY',
            'AUD', 'BRL', 'CAD', 'CNY', 'HKD', 'IDR', 'ILS', 'INR', 'KRW', 'MXN', 'MYR', 'NZD', 'PHP', 'SGD', 'THB',
            'ZAR',
        ];

        $labels = array_combine($currencies, array_map(fn ($s) => IntlCurrency::getName($s, locale: 'en_US'), $currencies));
        ksort($labels);

        return $response->withJson($labels);
    }
}
