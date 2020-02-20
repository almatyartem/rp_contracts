<?php

namespace ApiSdk\Contracts;

interface RequestProvider
{
    /**
     * @throw RequestException
     * @return array
     */
    public function request(string $api, string $method, string $url, array $data = [], array $addHeaders = []) : array;
}
