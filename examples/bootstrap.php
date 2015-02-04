<?php

use EvdB\OpenExchangeRates\OpenExchangeRates;
use EvdB\OpenExchangeRates\Api;
use GuzzleHttp\Client;

include dirname(__DIR__) . '/vendor/autoload.php';

$app_id = $argv[1];
$guzzle = new Client(['base_url' => OpenExchangeRates::getBaseUrl(false)]);
$exchange = new OpenExchangeRates(compact('app_id'), $guzzle);