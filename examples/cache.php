<?php

include "bootstrap.php";

//enable in-memory cache
\GuzzleHttp\Subscriber\Cache\CacheSubscriber::attach($guzzle);

//enable debug mode to see outgoing requests
$client->debug();

//request is only executed once, promised
$client->latest();
$client->latest();
