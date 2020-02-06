<?php

namespace ApiSdk;

use Illuminate\Support\ServiceProvider;

class GatewayServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if($apiUrl = env('GATEWAY_API_URL') and $apiEnv = env('GATEWAY_API_ENV') and $apiApp = env('GATEWAY_API_APP') and $apiToken = env('GATEWAY_API_APP_TOKEN'))
        {
            $this->app->singleton('ApiSdk\GatewayApi', function($app) use ($apiUrl, $apiEnv, $apiApp, $apiToken)
            {
                return new GatewayApi($app->make('GuzzleHttp\Client'), $apiUrl, $apiEnv, $apiApp, $apiToken, env('APP_ENV') == 'local');
            });

            if($authClientId = env('AUTH_API_CLIENT_ID') and $authClientSecret = env('AUTH_API_CLIENT_SECRET'))
            {
                $this->app->singleton('ApiSdk\AuthApi', function($app) use ($authClientId, $authClientSecret)
                {
                    return new AuthApi($app->make('ApiSdk\GatewayApi'), $authClientId, $authClientSecret, env('APP_URL') . '/oauth_callback');
                });
            }

            $this->app->bind('coreapi',function($app){
                return $app->make('ApiSdk\CoreApi');
            });

            $this->app->bind('reportsapi',function($app){
                return $app->make('ApiSdk\ReportsApi');
            });

            $this->app->bind('filesapi',function($app){
                return $app->make('ApiSdk\FilesApi');
            });

            $this->app->bind('authapi',function($app){
                return $app->make('ApiSdk\AuthApi');
            });
        }
    }
}
