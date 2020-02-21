<?php

namespace ApiSdk;

use ApiSdk\Contracts\RequestProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class GatewayRequestProvider implements RequestProvider
{
    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var string
     */
    public $env;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    public $app;

    /**
     * @var bool
     */
    protected $isDebug;

    /**
     * GatewayApi constructor.
     * @param Client $httpClient
     * @param string $endpoint
     * @param string $env
     * @param string $app
     * @param string $token
     * @param bool $isDebug
     */
    public function __construct(Client $httpClient, string $endpoint, string $env, string $app, string $token, bool $isDebug = false)
    {
        $this->httpClient = $httpClient;
        $this->endpoint = $endpoint;
        $this->env = $env;
        $this->app = $app;
        $this->token = $token;
        $this->isDebug = $isDebug;
    }

    /**
     * @param string $api
     * @param string $method
     * @param string $url
     * @param array $data
     * @param array $addHeaders
     * @return mixed
     * @throws RequestProviderException
     */
    public function request(string $api, string $method, string $url, array $data = [], array $addHeaders = [])
    {
        $options = [];

        if($data and $method != 'get')
        {
            $options['json'] = $data;
        }

        $options['headers'] = ['Accept' => 'application/json'];

        $options['headers']['X-App'] = $this->app;
        $options['headers']['X-App-Token'] = $this->token;

        if($addHeaders)
        {
            $options['headers'] = array_merge($options['headers'], $addHeaders);
        }

        if($this->isDebug)
        {
            $url .= (strpos($url,'?') ? '&' : '?').'XDEBUG_SESSION_START=PHPSTORM';
        }

        try
        {
            $response = $this->httpClient->request($method, $this->endpoint . '/' . $this->env . '/' . $api . '/' . $url, $options);
        }
        catch(RequestException $exception)
        {
            throw new RequestProviderException($exception);
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
