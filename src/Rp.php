<?php

namespace RpContracts;

interface Rp
{
    public function request(string $url, string $method = 'get', array $data = [], array $addHeaders = [], bool $postAsForm = false) : Response;
}
