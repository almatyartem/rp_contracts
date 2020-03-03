<?php

namespace ApiSdk;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class RequestProviderException
 * @package ApiSdk
 */
class RequestProviderException extends \Exception
{
    /**
     * @var array|string|null
     */
    protected $error;

    /**
     * RequestProviderException constructor.
     * @param RequestException $exception
     */
    function __construct(RequestException $exception)
    {
        if($response = $exception->getResponse() and $contents = $response->getBody()->getContents())
        {
            if($contents = json_decode($contents, true))
            {
                if(isset($contents['error']))
                {
                    $this->error = $contents['error'];
                }
            }
        }
        elseif($exception->getCode() != 400)
        {
            Log::error($exception->getCode().' '.$exception->getMessage());
        }

        parent::__construct($exception->getMessage());
    }

    public function getError()
    {
        return $this->error;
    }
}
