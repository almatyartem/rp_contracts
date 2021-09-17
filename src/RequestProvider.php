<?php

namespace RpContracts;

interface RequestProvider
{
    /**
     * @param string $url
     * @param string $method
     * @param array $data
     * @param array $addHeaders
     * @param bool $postAsForm
     * @param int|null $cacheTtl
     * @param bool $ignoreCache
     * @return Response
     */
    public function request(
        string $url,
        string $method = 'get',
        array $data = [],
        array $addHeaders = [],
        bool $postAsForm = false,
        int $cacheTtl = null,
        bool $ignoreCache = false
    ) : Response;

    /**
     * @param RequestData $data
     * @return Response
     */
    public function performRequest(RequestData $data) : Response;
}
