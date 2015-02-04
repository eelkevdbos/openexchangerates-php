<?php namespace EvdB\OpenExchangeRates;


use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\RequestInterface;

class OpenExchangeRates implements OpenExchangeRatesInterface
{

    use Configuration\Configurable;

    /**
     * OpenExchangeRates.org base url for API
     *
     * @var string
     */
    const BASE_URL = '{protocol}://openexchangerates.org/api/';

    /**
     * Available request query params
     *
     * @var array
     */
    static $availableRequestParams = ['base', 'app_id', 'symbols', 'start', 'end', 'prettyprint'];

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @param array $options
     * @param ClientInterface $client
     */
    public function __construct(array $options, ClientInterface $client)
    {
        $this->setOptions($options, false);
        $this->client = $client;
    }

    public function latest()
    {
        $request = $this->createRequest(
            'GET',
            'latest.json',
            $this->getRequestOptions()
        );

        return $this->handleRequest($request);
    }

    public function currencies()
    {
        $request = $this->createRequest(
            'GET',
            'currencies.json',
            $this->getRequestOptions()
        );

        return $this->handleRequest($request);
    }

    public function historical($date)
    {
        $queryDate = static::getFormattedQueryDate($date);

        $request = $this->createRequest(
            'GET',
            sprintf('historical/%s.json', $queryDate),
            $this->getRequestOptions()
        );

        return $this->handleRequest($request);
    }

    public function timeSeries($startDate, $endDate)
    {
        $query = [
            'start' => static::getFormattedQueryDate($startDate),
            'end'   => static::getFormattedQueryDate($endDate)
        ];

        $request = $this->createRequest(
            'GET',
            'time-series.json',
            array_replace_recursive($this->getRequestOptions(), compact('query'))
        );

        $this->handleRequest($request);
    }

    public function convert($value, $from, $to)
    {
        $request = $this->createRequest(
            'GET',
            sprintf('convert/%s/%s/%s', $value, $from, $to),
            $this->getRequestOptions()
        );

        return $this->handleRequest($request);
    }

    /**
     * Prettyprints output and uses ClientInterface debugger
     *
     * @author Eelke van den Bos <eelkevdbos@gmail.com>
     */
    public function debug()
    {
        $this->setOption('prettyprint', 1);
        $this->client->setDefaultOption('debug', true);
    }

    /**
     * Proxy the creation of the request to the ClientInterface implementation
     *
     * @param $method
     * @param null $url
     * @param array $options
     * @author Eelke van den Bos <eelkevdbos@gmail.com>
     * @return RequestInterface
     */
    protected function createRequest($method, $url = null, $options = [])
    {
        return $this->client->createRequest($method, $url, $options);
    }

    /**
     * Proxy the sending of the request to the ClientInterface implementation
     *
     * @param RequestInterface $request
     * @author Eelke van den Bos <eelkevdbos@gmail.com>
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    protected function sendRequest(RequestInterface $request)
    {
        return $this->client->send($request);
    }

    /**
     * @param RequestInterface $request
     * @author Eelke van den Bos <eelkevdbos@gmail.com>
     * @return \GuzzleHttp\Stream\StreamInterface|null
     */
    protected function handleRequest(RequestInterface $request)
    {
        $response = $this->sendRequest($request);

        return $response->getBody();
    }

    /**
     * @author Eelke van den Bos <eelkevdbos@gmail.com>
     * @return array
     */
    protected function getRequestOptions()
    {
        return ['query' => $this->getRequestQuery()];
    }

    /**
     * Filters out specific options required for the request
     *
     * @author Eelke van den Bos <eelkevdbos@gmail.com>
     * @return array
     */
    protected function getRequestQuery()
    {
        return $this->getOptions(static::getAvailableRequestParams());
    }

    /**
     * Return a valid format
     *
     * @param \DateTimeInterface|int|string $date
     * @author Eelke van den Bos <eelkevdbos@gmail.com>
     * @throws \InvalidArgumentException
     * @return string
     */
    static function getFormattedQueryDate($date)
    {
        $dateQuery = false;

        if ($date instanceof \DateTimeInterface) {

            $dateQuery = $date->format('Y-m-d');

        } else if (is_numeric($date)) {

            //suspect unix timestamp given
            $dateQuery = date('Y-m-d', $date);

        } else if (is_string($date) && preg_match('/[\d]{4}-[\d]{2}-[\d]{2}/', $date) === 1) {

            $dateQuery = $date;

        }

        if ($dateQuery === false) {
            throw new \InvalidArgumentException('Date argument could not be resolved');
        }

        return $dateQuery;
    }

    public static function getAvailableRequestParams()
    {
        return static::$availableRequestParams;
    }

    public static function getBaseUrl($secure = true)
    {
        return str_replace('{protocol}', $secure === true ? 'https' : 'http', static::BASE_URL);
    }

}