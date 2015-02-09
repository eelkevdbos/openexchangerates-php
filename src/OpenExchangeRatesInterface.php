<?php namespace EvdB\OpenExchangeRates;

interface OpenExchangeRatesInterface
{

    /**
     * @return \GuzzleHttp\Stream\StreamInterface|null
     */
    public function latest();

    /**
     * @return \GuzzleHttp\Stream\StreamInterface|null
     */
    public function currencies();

    /**
     * @return \GuzzleHttp\Stream\StreamInterface|null
     */
    public function historical($date);

    /**
     * @return \GuzzleHttp\Stream\StreamInterface|null
     */
    public function timeSeries($startDate, $endDate);

    /**
     * @return \GuzzleHttp\Stream\StreamInterface|null
     */
    public function convert($value, $from, $to);

    /**
     * @return \GuzzleHttp\Stream\StreamInterface|null
     */
    public function jsonp($method, array $args, $callback);

}
