<?php

namespace ApiSdk;

class CoreApi
{
    /**
     * @var GatewayApi
     */
    public $gatewayApi;

    /**
     * @var string
     */
    protected $coreAppCode = 'core';

    /**
     * CoreApi constructor.
     * @param GatewayApi $gatewayApi
     */
    public function __construct(GatewayApi $gatewayApi)
    {
        $this->gatewayApi = $gatewayApi;
    }

    /**
     * @param string $entity
     * @param array $where
     * @param array $addParams
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function find(string $entity, array $where = [], array $addParams = []) : ?array
    {
        $params = [];

        if($where)
        {
            $params['filter'] = $where;
        }

        $params = array_merge($params, $addParams);

        return $this->call($entity, 'all', null, $params);
    }

    /**
     * @param string $entity
     * @param array $where
     * @param array $addParams
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function findFirst(string $entity, array $where = [], array $addParams = []) : ?array
    {
        $addParams['count'] = 1;

        return $this->find($entity, $where, $addParams)[0] ?? null;
    }

    /**
     * @param string $entity
     * @param array $data
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function create(string $entity, array $data) : ?array
    {
        $result = $this->call($entity, 'create', null, $data);

        if(isset($result['error']['validation_errors']))
        {
            throw new \Exception(json_encode($result['error']['validation_errors']), 666);
        }

        return $result;
    }

    /**
     * @param string $entity
     * @param $id
     * @param array $data
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function patch(string $entity, $id, array $data) : ?array
    {
        $result = $this->call($entity, 'patch', $id, $data);

        if(isset($result['error']['validation_errors']))
        {
            throw new \Exception(json_encode($result['error']['validation_errors']), 666);
        }

        return $result;
    }

    /**
     * @param string $entity
     * @param $id
     * @param array $with
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function show(string $entity, $id, $with = []) : ?array
    {
        return $this->call($entity, 'show', $id, $with ? ['with' => $with] : []);
    }

    /**
     * @param string $entity
     * @param $id
     * @param array $with
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function delete(string $entity, $id, $with = []) : bool
    {
        $result = $this->call($entity, 'delete', $id, $with ? ['with' => $with] : []);

        if(isset($result['error']['relations_exist']))
        {
            throw new \Exception(json_encode($result['error']['relations_exist']), 666);
        }

        return $result['success'] ?? false;
    }

    /**
     * @param string $entity
     * @param string $method
     * @param null $id
     * @param array $params
     * @return array|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function call(string $entity, string $method, $id = null, array $params = []) : ?array
    {
        $getParams = [];

        switch($method)
        {
            case 'create':
                $requestMethod = 'post';
                break;
            case 'delete':
                $requestMethod = 'delete';
                break;
            case 'patch':
                $requestMethod = 'patch';
                break;
            default:
                $requestMethod = 'get';
                $getParams = $params;
        }


        $uri = 'crud/'.$entity.($id ? '/'.$id : '').($getParams ? '?'.http_build_query($getParams) : '');

        $response = $this->gatewayApi->request($this->coreAppCode, $requestMethod, $uri, $params);

        $data = $this->gatewayApi->getData($response);

        return $data;
    }
}
