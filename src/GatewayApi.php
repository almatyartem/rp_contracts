<?php

namespace ApiSdk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\ClientException;

class GatewayApi
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
     * @param string $clientId
     * @param string $clientSecret
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
     * @param string $url
     * @param string $method
     * @param array $data
     * @param array $addHeaders
     * @param bool $needAuth
     * @return Response
     * @throws GuzzleException
     */
    public function request(string $api, string $method, string $url, array $data = [], array $addHeaders = []) : Response
    {
        $options = [];

        if($data and $method != 'get')
        {
            $options['json'] = $data;
        }

        if($method == 'get')
        {
            $options['headers'] = ['Content-type' => 'application/x-www-form-urlencoded', 'Accept' => 'application/json'];
        }
        else
        {
            $options['headers'] = ['Content-type' => 'application/json', 'Accept' => 'application/json'];
        }

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
            $result = $this->httpClient->request($method, $this->endpoint.'/'.$this->env.'/'.$api.'/'. $url, $options);
        }
        catch(ClientException $exception)
        {
            return $exception->getResponse();
        }

        return $result;
    }

    /**
     * @param ResponseInterface $response
     * @return mixed
     */
    public function getData(ResponseInterface $response)
    {
        return json_decode($response->getBody()->getContents(), true);
    }
}
