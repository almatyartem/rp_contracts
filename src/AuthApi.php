<?php

namespace ApiSdk;

use GuzzleHttp\Exception\GuzzleException;

class AuthApi
{
    /**
     * @var GatewayApi
     */
    public $gatewayApi;

    /**
     * @var string
     */
    protected $authAppCode = 'auth';

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var string
     */
    protected $oauthCallback;

    /**
     * CoreApi constructor.
     * @param GatewayApi $gatewayApi
     */
    public function __construct(GatewayApi $gatewayApi, string $clientId, string $clientSecret, string $oauthCallback)
    {
        $this->gatewayApi = $gatewayApi;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->oauthCallback = $oauthCallback;
    }

    /**
     * @return null
     * @throws GuzzleException
     */
    protected function getAppAccessToken()
    {
        $response = $this->gatewayApi->request($this->authAppCode , 'post','oauth/token',  [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ], [], false);

        $data = $this->gatewayApi->getData($response);

        if($data and isset($data['access_token']) and $data['access_token'])
        {
            return $data['access_token'];
        }

        return null;
    }

    /**
     * @param $code
     * @return string|null
     */
    public function getClientToken($code) : ?string
    {
        $this->accessToken = null;

        $response = $this->gatewayApi->request($this->authAppCode , 'post','oauth/token',  [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->oauthCallback,
            'code' => $code,
        ], []);

        $data = $this->gatewayApi->getData($response);

        if($data and isset($data['access_token']) and $data['access_token'])
        {
            return $data['access_token'];
        }

        return null;
    }

    /**
     * @param $token
     * @return mixed
     */
    public function getUserByToken($token)
    {
        $response = $this->gatewayApi->request($this->authAppCode , 'get','api/user?env='.$this->gatewayApi->env.'&app='.$this->gatewayApi->app,  [], [
            'Authorization' => 'Bearer ' .$token
        ]);

        $user = $this->gatewayApi->getData($response);

        return $user;
    }
}
