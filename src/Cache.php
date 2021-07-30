<?php

namespace RpContracts;

interface Cache
{
    /**
     * @param string $uri
     * @param Response $result
     * @param int|null $ttl
     * @return bool
     */
    public function put(string $uri, Response $result, int $ttl = null) : bool;

    /**
     * @param string $uri
     * @return Response|null
     */
    public function get(string $uri) : ?Response;

    /**
     * @param string $uri
     * @return bool
     */
    public function has(string $uri) : bool;
}
