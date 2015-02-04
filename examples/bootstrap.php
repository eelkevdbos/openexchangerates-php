<?php

use EvdB\OpenExchangeRates\OpenExchangeRates;
use EvdB\OpenExchangeRates\Api;
use GuzzleHttp\Client;

include dirname(__DIR__) . '/vendor/autoload.php';

$app_id = 'YOUR_APP_ID_HERE';
$base_url = Api::getBaseUrl(false); //with FALSE retrieves HTTP url, defaults to HTTPS

$client = new OpenExchangeRates(
    compact('app_id'),
    $guzzle = new Client(compact('base_url'))
);