<?php

namespace RpContracts;

use Throwable;

interface Response
{
    public function getRawContents() : ?string;

    /**
     * @return array|null
     */
    public function getContents() : ?array;

    /**
     * @return array|Throwable[]|null
     */
    public function getErrorsBag() : ?array;

    /**
     * @return Throwable|null
     */
    public function getLastException() : ?Throwable;

    /**
     * @return bool
     */
    public function isSuccess() : bool;

    /**
     * @return int
     */
    public function getStatusCode() : int;
}
