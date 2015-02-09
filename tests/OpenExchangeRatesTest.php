<?php

use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\History;
use EvdB\OpenExchangeRates\OpenExchangeRates;
use GuzzleHttp\Subscriber\Mock;
use GuzzleHttp\Message\Response;

class OpenExchangeRatesTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * @var \GuzzleHttp\Subscriber\History
     */
    protected $history;


    protected function buildClient($options = [], $enableHistory = true)
    {
        $client = new Client($options);

        if ($enableHistory === true) {
            $this->history = new History();
            $client->getEmitter()->attach($this->history);
        }

        return $client;
    }

    protected function mockResponses(\GuzzleHttp\ClientInterface &$client, array $responses)
    {
        $client->getEmitter()->attach(new Mock($responses));
    }

    protected function buildExchangeRateClient($options = null, $client = null)
    {
        return new OpenExchangeRates(
            $options ?: ['app_id' => 'testappid'],
            $client ?: $this->buildClient()
        );
    }

    public function testCreationOfRequests()
    {
        $exchange = $this->buildExchangeRateClient(null, $client = $this->buildClient());

        //mock 5 responses
        $this->mockResponses($client, array_fill(0, 5, new Response(200)));

        $interfaceMethods = [
            'latest' => [],
            'currencies' => [],
            'historical' => [DateTime::createFromFormat('Y-m-d', '2015-12-29')],
            'timeSeries' => [DateTime::createFromFormat('Y-m-d', '2015-12-30'), DateTime::createFromFormat('Y-m-d', '2015-12-31')],
            'convert' => [rand(0, 100), 'USD', 'EUR']
        ];

        foreach ($interfaceMethods as $method => $args) {
            call_user_func_array([$exchange, $method], $args);
        }

        $this->assertCount(5, $this->history, '5 requests should be made');
    }

    /**
     * @expectedException \EvdB\OpenExchangeRates\Exception\ResourceNotFound
     */
    public function testJsonpCallbackPresence()
    {
        $exchange = $this->buildExchangeRateClient(null, $client = $this->buildClient());

        $this->mockResponses($client, [new Response(200)]);

        $exchange->jsonp('latest', [], 'testMyCallback');

        $this->assertEquals('testMyCallback', $this->history->getLastRequest()->getQuery()->get('callback'));

        //will throw expected exception
        $exchange->jsonp('invalid', [], 'myInvalidCallback');
    }

    /**
     * @expectedException \EvdB\OpenExchangeRates\Exception\InvalidDateArgument
     */
    public function testQueryDateFormatter()
    {
        $dateFromDateTime = OpenExchangeRates::getFormattedQueryDate(DateTime::createFromFormat('Y-m-d', '2015-12-31'));

        $this->assertEquals('2015-12-31', $dateFromDateTime);

        $dateFromTimestamp = OpenExchangeRates::getFormattedQueryDate($time = time());

        $this->assertEquals(date('Y-m-d', $time), $dateFromTimestamp);

        $dateFromString = OpenExchangeRates::getFormattedQueryDate('2015-05-10');

        $this->assertEquals('2015-05-10', $dateFromString);

        //throws expected exception
        $mumboJumboDateTime = OpenExchangeRates::getFormattedQueryDate('this-is-invalid');

        if (!interface_exists('DateTimeInterface')) {

            eval('
                interface DateTimeInterface {
                    public function format($format);
                }

                class MyDate extends DateTime implements DateTimeInterface {}
            ');

            $dateTimeImplementation = new MyDate();
            $dateTimeImplementation->setTimestamp($mytime = time());

            $this->assertEquals(date('Y-m-d', $mytime), OpenExchangeRates::getFormattedQueryDate($dateTimeImplementation));
        }
    }

    public function testBuildingBaseUrl()
    {
        $http = OpenExchangeRates::getBaseUrl(false);
        $https = OpenExchangeRates::getBaseUrl(true);

        $this->assertTrue(strpos($http, 'http://') !== false);
        $this->assertTrue(strpos($https, 'https://') !== false);
    }

    public function testDebugMode()
    {
        $exchange = $this->buildExchangeRateClient(null, $client = $this->buildClient());

        $exchange->debug();

        //prettyprint should be enabled
        $this->assertEquals(1, $exchange->getOption('prettyprint'));

        //debug should be enabled on the clients default options
        $this->assertTrue($client->getDefaultOption('debug'));
    }

}
