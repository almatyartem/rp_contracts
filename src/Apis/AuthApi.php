<?php

namespace ApiSdk;

use ApiSdk\Contracts\RequestProvider;
use GuzzleHttp\Exception\GuzzleException;

class AuthApi
{
    /**
     * @var RequestProvider
     */
    public $provider;

    public $api;

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
     * @var string
     */
    public $env;

    /**
     * @var string
     */
    public $app;

    /**
     * AuthApi constructor.
     * @param RequestProvider $provider
     * @param string $clientId
     * @param string $clientSecret
     * @param string $oauthCallback
     * @param string $env
     * @param string $app
     * @param null $api
     */
    public function __construct(RequestProvider $provider, string $clientId, string $clientSecret, string $oauthCallback, string $env, string $app, $api = null)
    {
        $this->provider = $provider;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->oauthCallback = $oauthCallback;
        $this->env = $env;
        $this->app = $app;
        $this->api = $api ?? 'files';
    }

    /**
     * @return null
     * @throws GuzzleException
     */
    protected function getAppAccessToken()
    {
        $data = $this->provider->request($this->api , 'post','oauth/token',  [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ], []);

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
        $data = $this->provider->request($this->api , 'post','oauth/token',  [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->oauthCallback,
            'code' => $code,
        ], []);

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
        return $this->provider->request($this->api , 'get','api/user?env='.$this->env.'&app='.$this->app,  [], [
            'Authorization' => 'Bearer ' .$token
        ]);
    }
}
