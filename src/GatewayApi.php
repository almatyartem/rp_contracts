<?php

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

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
    protected $env;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var string
     */
    protected $token = null;

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
    public function __construct(Client $httpClient, string $endpoint, string $env, string $clientId, string $clientSecret, bool $isDebug = false)
    {
        $this->httpClient = $httpClient;
        $this->endpoint = $endpoint;
        $this->env = $env;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->isDebug = $isDebug;
    }

    /**
     * @param string $api
     * @param string $uri
     * @param string $method
     * @param array $postData
     * @return Response
     * @throws GuzzleException
     */
    public function proxy(string $api, string $uri, string $method, array $postData = []) : Response
    {
        return $this->request('api/proxy/'.$api, 'POST', $postData, [
            'X-Method' => $method,
            'X-Uri' => $uri,
            'X-Env' => $this->env,
        ]);
    }

    /**
     * @throws GuzzleException|\Exception
     */
    protected function needAuth() : void
    {
        if(!$this->token)
        {
            if(!$this->token = $this->getData($this->request('oauth/token', 'post',
                [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ], [], false))['access_token'] ?? null)
            {
                throw new \Exception('Gateway auth error');
            }
        }
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
    public function request(string $url, string $method, array $data = [], array $addHeaders = [], $needAuth = true) : Response
    {
        $options = [];

        if($data)
        {
            $options['form_params'] = $data;
        }

        $options['headers'] = [
            'Content-type' => 'application/x-www-form-urlencoded',
            'Accept' => 'application/json'
        ];

        if($needAuth)
        {
            $this->needAuth();

            $options['headers']['Authorization'] = 'Bearer ' . $this->token;
        }

        if($addHeaders)
        {
                $options['headers'] = array_merge($options['headers'], $addHeaders);
        }

        if($this->isDebug)
        {
            $data['XDEBUG_SESSION_START'] = 'PHPSTORM';
        }

        $result = $this->httpClient->request($method, $this->endpoint . '/' . $url, $options);

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
