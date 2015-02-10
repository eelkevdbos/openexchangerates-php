openexchangerates-php
============

[![Build Status](https://travis-ci.org/eelkevdbos/openexchangerates-php.svg)](https://travis-ci.org/eelkevdbos/openexchangerates-php) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/eelkevdbos/openexchangerates-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/eelkevdbos/openexchangerates-php/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/eelkevdbos/openexchangerates-php/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/eelkevdbos/openexchangerates-php/?branch=master)

OpenExchangeRates.org API Wrapper in PHP

## Installation

`composer require eelkevdbos/openexchangerates-php`

## Basic Usage

```php
use EvdB\OpenExchangeRates\OpenExchangeRates;
use GuzzleHttp\Client;

//construct request client
$client = new Client(['base_url' => OpenExchangeRates::getBaseUrl(true)]);

//construct openexchangerates instance
$exchange = new OpenExchangeRates(['app_id' => 'THIS_IS_YOUR_APP_ID'], $client);

//available methods below, some methods require the purchase of a specific openexchangerates.org plan
$exchange->latest();
$exchange->historical('2015-01-01');
$exchange->timeSeries('2015-01-01', '2015-01-02');
$exchange->convert('12', 'USD', 'EUR');
$exchange->currencies();

//all methods above can also be called with a jsonp callback
$exchange->jsonp('latest', [], 'myCallbackName');
$exchange->jsonp('timeSeries', ['2015-01-01', '2015-01-02'], 'myTimeseriesCallback');

```

All methods return the json data structure as noted in the [API docs](https://openexchangerates.org/documentation) provided by OpenExchangeRates.org.

## Simple caching

Guzzle provides us with a cache subscriber for the request client. For more information, please read the [cache-subscriber](https://github.com/guzzle/cache-subscriber) docs.

## Disclaimer

This project is not affiliated in any way with OpenExchangeRates.org. It is intended to provide a useful service and comes with no warranty or any kind. The author is not responsible for any damages or problems incurred during usage of the API. Use at your own risk.
