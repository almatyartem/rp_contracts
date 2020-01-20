<?php

namespace ApiSdk\Exceptions;

class CoreDeleteException extends \Exception
{
    public $errors;

    /**
     * ValidationError constructor.
     * @param $relations
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($relations, $message = "", $code = 0, \Throwable $previous = null)
    {
        $this->relations = $relations;

        parent::__construct($message, $code, $previous);
    }
}