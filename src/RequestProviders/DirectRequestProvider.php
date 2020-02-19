<?php

namespace App;

use ApiSdk\Contracts\RequestProvider;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;

class DirectRequestProvider implements RequestProvider
{
    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var bool
     */
    protected $isDebug;

    /**
     * DirectRequestProvider constructor.
     * @param Client $httpClient
     * @param bool $isDebug
     */
    public function __construct(Client $httpClient, bool $isDebug = false)
    {
        $this->httpClient = $httpClient;
        $this->isDebug = $isDebug;
    }

    /**
     * @param string $api
     * @param string $method
     * @param string $url
     * @param array $data
     * @param array $addHeaders
     * @return array
     */
    public function request(string $api, string $method, string $url, array $data = [], array $addHeaders = []) : array
    {
        $options = [];

        if($data and $method != 'get')
        {
            $options['json'] = $data;
        }

        $options['headers'] = ['Accept' => 'application/json'];

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
            $response = $this->httpClient->request($method, $api.'/'. $url, $options);
        }
        catch(ClientException $exception)
        {
            $response = $exception->getResponse();
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
