<?php namespace EvdB\OpenExchangeRates;


interface OpenExchangeRatesInterface {

    public function latest();

    public function currencies();

    public function historical($date);

    public function timeSeries($startDate, $endDate);

    public function convert($value, $from, $to);

}