<?php

namespace ApiSdk;

class ReportsApi
{
    /**
     * @var GatewayApi
     */
    protected $gatewayApi;

    /**
     * ReportsApi constructor.
     * @param GatewayApi $gatewayApi
     */
    public function __construct(GatewayApi $gatewayApi)
    {
        $this->gatewayApi = $gatewayApi;
    }

    /**
     * @param $command
     * @param null $params
     * @return mixed
     * @throws \ErrorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function call($command, $params = null)
    {
        $response = $this->exec($command, $params);

        if(!empty($response['status']) && empty($response['error']) && !empty($response['data']))
        {
            return $response['data'];
        }
        else
        {
            throw new \ErrorException('Something gone wrong on reports api');
        }
    }

    /**
     * @param $command
     * @param null $params
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function exec($command, $params = null)
    {
        $response = $this->gatewayApi->request('reports', 'post', $command, $params);

        if ($response->getStatusCode()>=300) {
            return [
                'status' => 1,
                'error' => 'Api execution error'
            ];
        }
        else
        {
            return $this->gatewayApi->getData($response);
        }
    }
}