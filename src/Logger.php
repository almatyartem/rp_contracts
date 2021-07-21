<?php

namespace RpContracts;

interface Logger
{
    /**
     * @param Response $result
     * @param array $requestData
     * @return mixed
     */
    public function log(Response $result, array $requestData);
}
