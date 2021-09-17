<?php

namespace RpContracts;

interface RequestData
{
    /**
     * @return string
     */
    public function getUrl() : string;

    /**
     * @return string
     */
    public function getMethod() : string;

    /**
     * @return array|null
     */
    public function getData() : ?array;

    /**
     * @return array|null
     */
    public function getHeaders() : ?array;

    /**
     * @return bool
     */
    public function postAsForm() : bool;

    /**
     * @return int|null
     */
    public function getCacheTtl() : ?int;
}
