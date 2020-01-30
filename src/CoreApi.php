<?php

namespace ApiSdk;

use ApiSdk\Exceptions\CoreDeleteException;
use ApiSdk\Exceptions\CoreValidationException;
use Illuminate\Validation\ValidationException;

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
     * @throws ValidationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(string $entity, array $data) : ?array
    {
        $result = $this->call($entity, 'create', null, $data);

        if(isset($result['error']['validation_errors']))
        {
            throw new ValidationException($result['error']['validation_errors']);
        }

        return $result;
    }

    /**
     * @param string $entity
     * @param $id
     * @param array $data
     * @return array|null
     * @throws ValidationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function patch(string $entity, $id, array $data) : ?array
    {
        $result = $this->call($entity, 'patch', $id, $data);

        if(isset($result['error']['validation_errors']))
        {
            throw new ValidationException($result['error']['validation_errors']);
        }

        return $result;
    }

    /**
     * @param string $entity
     * @param $id
     * @param array $with
     * @return array|null
     * @throws CoreValidationException
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
     * @throws CoreDeleteException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(string $entity, $id, $with = []) : bool
    {
        $data = $this->call($entity, 'delete', $id, $with ? ['with' => $with] : []);

        if(isset($data['error']['relations_exist']))
        {
            throw new CoreDeleteException($data['error']['relations_exist']);
        }

        return $data['success'] ?? false;
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
