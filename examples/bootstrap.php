<?php

use EvdB\OpenExchangeRates\OpenExchangeRates;
use EvdB\OpenExchangeRates\Api;
use GuzzleHttp\Client;

include dirname(__DIR__) . '/vendor/autoload.php';

$app_id = $argv[1];
$base_url = Api::getBaseUrl(false); //with FALSE retrieves HTTP url, defaults to HTTPS

$client = new OpenExchangeRates(
    compact('app_id'),
    $guzzle = new Client(compact('base_url'))
);