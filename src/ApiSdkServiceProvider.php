<?php

namespace ApiSdk;

use Illuminate\Support\ServiceProvider;

class ApiSdkServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('ApiSdk\GatewayApi', function ($app) {
            return new GatewayRequestProvider($app->make('GuzzleHttp\Client'), env('GATEWAY_API_URL'), env('GATEWAY_API_ENV'), env('GATEWAY_API_APP'),
                env('GATEWAY_API_APP_TOKEN'), env('APP_ENV')=='local');
        });

        $this->app->singleton('ApiSdk\Contracts\RequestProvider',function($app){
            return env('GATEWAY_API_URL') ? $app->make('ApiSdk\GatewayApi') : $app->make('ApiSdk\DirectRequestProvider');
        });

        $this->app->singleton('ApiSdk\AuthApi', function ($app) {
            return new AuthApi($app->make('ApiSdk\Contracts\RequestProvider'), env('AUTH_API_CLIENT_ID'), env('AUTH_API_CLIENT_SECRET'),
                env('APP_URL').'/oauth_callback', env('GATEWAY_API_ENV'), env('GATEWAY_API_APP'), env('AUTHAPI_ENDPOINT'));
        });

        $this->app->when('ApiSdk\DirectRequestProvider')
            ->needs('$isDebug')
            ->give(env('APP_ENV')=='local');

        $this->app->when('ApiSdk\CoreApi')
            ->needs('$api')
            ->give(env('COREAPI_ENDPOINT'));

        $this->app->when('ApiSdk\FilesApi')
            ->needs('$api')
            ->give(env('FILESAPI_ENDPOINT') ? env('FILESAPI_ENDPOINT').'/'.env('GATEWAY_API_ENV') : null);

        $this->app->bind('coreapi',function($app){
            return $app->make('ApiSdk\CoreApi');
        });

        $this->app->bind('filesapi',function($app){
            return $app->make('ApiSdk\FilesApi');
        });

        $this->app->bind('authapi',function($app){
            return $app->make('ApiSdk\AuthApi');
        });
    }
}
