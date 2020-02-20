<?php

namespace ApiSdk\Contracts;

use ApiSdk\RequestProviderException;

interface RequestProvider
{
    /**
     * @param string $api
     * @param string $method
     * @param string $url
     * @param array $data
     * @param array $addHeaders
     * @return mixed
     * @throws RequestProviderException
     */
    public function request(string $api, string $method, string $url, array $data = [], array $addHeaders = []);
}
