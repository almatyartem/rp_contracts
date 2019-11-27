<?php

namespace App;

use Illuminate\Validation\ValidationException;

class CoreApi
{
    /**
     * @var GatewayApi
     */
    public $gatewayApi;

    /**
     * @var bool
     */
    protected $isDebug;

    /**
     * @var string
     */
    protected $coreAppCode = 'core';

    /**
     * CoreApi constructor.
     * @param GatewayApi $gatewayApi
     * @param bool $isDebug
     */
    public function __construct(GatewayApi $gatewayApi, bool $isDebug = false)
    {
        $this->gatewayApi = $gatewayApi;
        $this->isDebug = $isDebug;
    }

    /**
     * @param string $entity
     * @param array $where
     * @param array $with
     * @param array $addParams
     * @return array|null
     * @throws ValidationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function find(string $entity, array $where = [], array $with = [], array $addParams = []) : ?array
    {
        $params = [];

        if($where)
        {
            $params['filter'] = $where;
        }

        if($with)
        {
            $addParams['with'] = $with;
        }

        $params = array_merge($params, $addParams);

        return $this->call($entity, 'all', null, $params);
    }

    /**
     * @param string $endpoint
     * @param array $where
     * @param array $with
     * @return array|null
     * @throws ValidationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function findFirst(string $entity, array $where = [], array $with = []) : ?array
    {
        return $this->find($entity, $where, $with)[0] ?? null;
    }

    /**
     * @param string $endpoint
     * @param array $data
     * @return array|null
     * @throws ValidationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(string $entity, array $data) : ?array
    {
        return $this->call($entity, 'create', null, $data);
    }

    /**
     * @param string $endpoint
     * @param $id
     * @param array $data
     * @return array|null
     * @throws ValidationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function patch(string $entity, $id, array $data) : ?array
    {
        return $this->call($entity, 'patch', $id, $data);
    }

    /**
     * @param string $entity
     * @param $id
     * @param array $with
     * @return array|null
     * @throws ValidationException
     * @throws \GuzzleHttp\Exception\GuzzleException
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
     * @throws ValidationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(string $entity, $id, $with = []) : bool
    {
        return $this->call($entity, 'delete', $id, $with ? ['with' => $with] : [])['success'] ?? false;
    }

    /**
     * @param string $entity
     * @param string $method
     * @param null $id
     * @param array $params
     * @return array|null
     * @throws ValidationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function call(string $entity, string $method, $id = null, array $params = []) : ?array
    {
        $getParams = [];

        switch($method)
        {
            case 'create':
                $requestMethod = 'POST';
                break;
            case 'delete':
                $requestMethod = 'DELETE';
                break;
            case 'patch':
                $requestMethod = 'PATCH';
                break;
            default:
                $requestMethod = 'GET';
                $getParams = $params;
        }

        if($this->isDebug)
        {
            $getParams['XDEBUG_SESSION_START'] = 'PHPSTORM';
        }

        $uri = 'crud/'.$entity.($id ? '/'.$id : '').($getParams ? '?'.http_build_query($getParams) : '');

        $response = $this->gatewayApi->proxy($this->coreAppCode, $uri, $requestMethod, $params);

        $data = $this->gatewayApi->getData($response);

        if($response->getStatusCode() >= 300)
        {
            if(isset($data['error']['validation_errors']))
            {
                throw new ValidationException($response['error']['validation_errors']);
            }
        }

        return $data;
    }
}
