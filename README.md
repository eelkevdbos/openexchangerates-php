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

## Simple caching

Guzzle provides us with a cache subscriber for the request client. For more information, please read the (https://github.com/guzzle/cache-subscriber)[cache-subscriber] docs.
