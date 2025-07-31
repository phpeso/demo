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
            'AED', 'AFN', 'ALL', 'AMD', 'AOA', 'ARS', 'AUD', 'AWG', 'AZN', 'BAM', 'BBD', 'BDT', 'BGN', 'BHD', 'BIF',
            'BMD', 'BND', 'BOB', 'BRL', 'BSD', 'BTN', 'BWP', 'BYN', 'BZD', 'CAD', 'CDF', 'CHF', 'CLP', 'CNY', 'COP',
            'CRC', 'CUP', 'CVE', 'DJF', 'DKK', 'DOP', 'DZD', 'EGP', 'ERN', 'ETB', 'EUR', 'FJD', 'FKP', 'GBP', 'GEL',
            'GHS', 'GIP', 'GMD', 'GNF', 'GTQ', 'GYD', 'HKD', 'HNL', 'HTG', 'HUF', 'IDR', 'ILS', 'INR', 'IQD', 'IRR',
            'ISK', 'JMD', 'JOD', 'JPY', 'KES', 'KGS', 'KHR', 'KMF', 'KPW', 'KRW', 'KWD', 'KYD', 'KZT', 'LAK', 'LBP',
            'LKR', 'LRD', 'LSL', 'LYD', 'MAD', 'MDL', 'MGA', 'MKD', 'MMK', 'MNT', 'MOP', 'MRU', 'MUR', 'MVR', 'MWK',
            'MXN', 'MYR', 'MZN', 'NAD', 'NGN', 'NIO', 'NOK', 'NPR', 'NZD', 'OMR', 'PAB', 'PEN', 'PGK', 'PHP', 'PKR',
            'PLN', 'PYG', 'QAR', 'RON', 'RSD', 'RUB', 'RWF', 'SAR', 'SBD', 'SCR', 'SDG', 'SEK', 'SGD', 'SHP', 'SLE',
            'SOS', 'SRD', 'SSP', 'STN', 'SVC', 'SYP', 'SZL', 'THB', 'TJS', 'TMT', 'TND', 'TOP', 'TRY', 'TTD', 'TWD',
            'TZS', 'UAH', 'UGX', 'USD', 'UYU', 'UZS', 'VND', 'VUV', 'WST', 'XAF', 'XCD', 'XCG', 'XDR', 'XOF', 'XPF',
            'YER', 'ZAR', 'ZMW',
        ];

        $labels = array_combine($currencies, array_map(fn ($s) => IntlCurrency::getName($s, locale: 'en_US'), $currencies));
        ksort($labels);

        return $response->withJson($labels);
    }
}
