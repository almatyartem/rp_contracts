<?php

namespace ApiSdk\Contracts;

interface RequestProvider
{
    public function request(string $api, string $method, string $url, array $data = [], array $addHeaders = []) : array;
}
