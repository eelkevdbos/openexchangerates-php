<?php namespace EvdB\OpenExchangeRates;

/**
 * Class Api
 *
 * Holds basic information about the API of the OpenExchangeRates.org service.
 *
 * @package EvdB\OpenExchangeRates
 */
class Api
{

    const BASE_URL = '{protocol}://openexchangerates.org/api/';

    static $availableRequestOptions = ['base', 'app_id', 'symbols', 'start', 'end', 'prettyprint'];

    public static function getAvailableRequestOptions()
    {
        return static::$availableRequestOptions;
    }

    public static function getBaseUrl($secure = true)
    {
        return str_replace('{protocol}', $secure === true ? 'https' : 'http', static::BASE_URL);
    }

}