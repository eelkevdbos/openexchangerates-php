<?php namespace EvdB\OpenExchangeRates;


use EvdB\OpenExchangeRates\Exception\ResourceNotFound;
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
    static $availableQueryParams = ['base', 'app_id', 'symbols', 'start', 'end', 'prettyprint'];

    /**
     * Available API methods
     *
     * @var array
     */
    static $availableApiMethods = ['latest', 'currencies', 'historical', 'timeSeries', 'convert'];

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var array
     */
    protected $queuedQueryParams = [];

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
        $this->queueQueryParam('start', static::getFormattedQueryDate($startDate));
        $this->queueQueryParam('end', static::getFormattedQueryDate($endDate));

        $request = $this->createRequest(
            'GET',
            'time-series.json',
            $this->getRequestOptions()
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
     * Call a public method with arguments and supply a jsonp callback argument
     *
     * @param string $method
     * @param array $args
     * @param string $callback
     */
    public function jsonp($method, array $args, $callback)
    {
        if (!in_array($method, static::getAvailableApiMethods())) {
            throw new ResourceNotFound("Resource {$method} not available");
        }

        $this->queueQueryParam('callback', $callback);

        return call_user_func_array([$this, $method], $args);
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
     * Build an array with ClientInterface options
     *
     * @author Eelke van den Bos <eelkevdbos@gmail.com>
     * @return array
     */
    protected function getRequestOptions()
    {
        return [
            'query' => array_merge(
                $this->getQueryParams(),
                $this->getQueuedQueryParams()
            )
        ];
    }

    /**
     * Filters out specific options required for the request
     *
     * @author Eelke van den Bos <eelkevdbos@gmail.com>
     * @return array
     */
    protected function getQueryParams()
    {
        return $this->getOptions(static::getAvailableQueryParams());
    }

    /**
     * Queue a query param for execution
     * @param $key
     * @param $value
     */
    protected function queueQueryParam($key, $value)
    {
        $this->queuedQueryParams[$key] = $value;
    }

    /**
     * Retrieve queued query params and empty the queue
     * @return array
     */
    protected function getQueuedQueryParams()
    {
        $params = $this->queuedQueryParams;
        $this->queuedQueryParams = [];
        return $params;
    }

    /**
     * Return a valid format
     *
     * @param \DateTimeInterface|int|string $date
     * @author Eelke van den Bos <eelkevdbos@gmail.com>
     * @throws \InvalidArgumentException
     * @return string
     */
    public static function getFormattedQueryDate($date)
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

    /**
     * @return array
     */
    public static function getAvailableQueryParams()
    {
        return static::$availableQueryParams;
    }

    /**
     * @return array
     */
    public static function getAvailableApiMethods()
    {
        return static::$availableApiMethods;
    }

    /**
     * @param bool $secure
     * @return mixed
     */
    public static function getBaseUrl($secure = true)
    {
        return str_replace('{protocol}', $secure === true ? 'https' : 'http', static::BASE_URL);
    }

}