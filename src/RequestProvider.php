<?php

namespace GuzzleWrapper;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;

class RequestProvider
{
    /**
     * @var Client
     */
    protected Client $httpClient;

    /**
     * @var string
     */
    protected string $endpoint;

    /**
     * @var int
     */
    protected int $attemptsCountWhenServerError;

    /**
     * @var int
     */
    protected int $sleepTimeBetweenAttempts;

    /**
     * RequestProvider constructor.
     * @param string $endpoint
     * @param int $attemptsCountWhenServerError
     * @param int $sleepTimeBetweenAttempts
     */
    public function __construct(
        string $endpoint,
        int $attemptsCountWhenServerError = 1,
        int $sleepTimeBetweenAttempts = 1
    )
    {
        $this->httpClient = new Client(['verify' => false]);
        $this->endpoint = $endpoint;
        $this->attemptsCountWhenServerError = $attemptsCountWhenServerError;
        $this->sleepTimeBetweenAttempts = $sleepTimeBetweenAttempts;
    }

    /**
     * @param string $url
     * @param array $data
     * @param array $addHeaders
     * @return ResultWrapper
     */
    public function get(string $url, array $data = [], array $addHeaders = []) : ResultWrapper
    {
        return $this->request($url, 'get', $data, $addHeaders);
    }

    /**
     * @param string $url
     * @param array $data
     * @param array $addHeaders
     * @param bool $postAsForm
     * @return ResultWrapper
     */
    public function post(string $url, array $data = [], array $addHeaders = [], bool $postAsForm = false) : ResultWrapper
    {
        return $this->request($url, 'post', $data, $addHeaders, $postAsForm);
    }

    /**
     * @param string $url
     * @param array $data
     * @param array $addHeaders
     * @param bool $postAsForm
     * @return ResultWrapper
     */
    public function patch(string $url, array $data = [], array $addHeaders = [], bool $postAsForm = false) : ResultWrapper
    {
        return $this->request($url, 'patch', $data, $addHeaders, $postAsForm);
    }

    /**
     * @param string $url
     * @param array $data
     * @param array $addHeaders
     * @param bool $postAsForm
     * @return ResultWrapper
     */
    public function delete(string $url, array $data = [], array $addHeaders = [], bool $postAsForm = false) : ResultWrapper
    {
        return $this->request($url, 'delete', $data, $addHeaders, $postAsForm);
    }

    /**
     * @param string $api
     * @param string $method
     * @param string $url
     * @param array $data
     * @param array $addHeaders
     * @param bool $postAsForm
     * @return ResultWrapper
     */
    protected function request(string $url, string $method = 'get', array $data = [], array $addHeaders = [], bool $postAsForm = false) : ResultWrapper
    {
        $options = [];

        if($method != 'get' and $data)
        {
            if($postAsForm)
            {
                $options['form_params'] = $data;
            }
            else
            {
                $options['json'] = $data;
            }
        }

        $options['headers'] = ['Accept' => 'application/json'];

        if($addHeaders)
        {
            $options['headers'] = array_merge($options['headers'], $addHeaders);
        }

        return $this->sendRequestHandler($url, $method, $options);
    }

    /**
     * @param string $method
     * @param $url
     * @param array $options
     * @return ResultWrapper
     */
    protected function sendRequestHandler($url, string $method, array $options = []) : ResultWrapper
    {
        $currentAttempt = 0;
        $response = null;
        $e = null;
        $errorsBag = [];

        do{
            if($currentAttempt > 0)
            {
                sleep($this->sleepTimeBetweenAttempts);
            }
            try
            {
                $response = $this->httpClient->request($method, $this->endpoint . '/' .$url, $options);
            }
            catch (\Throwable $e)
            {
                $errorsBag[] = $e;
            }

            $currentAttempt++;
        }
        while(($e instanceof ServerException) and ($currentAttempt <= $this->attemptsCountWhenServerError));

        return new ResultWrapper($response, ($errorsBag ? $errorsBag : null));
    }
}
