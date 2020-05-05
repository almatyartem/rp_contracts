<?php

namespace ApiSdk;

use Illuminate\Support\ServiceProvider;

class RpServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('ApiSdk\Contracts\RequestProvider',function($app){
            return env('GATEWAY_API_URL') ? $app->make('ApiSdk\GatewayApi') : $app->make('ApiSdk\DirectRequestProvider');
        });

        $this->app->when('ApiSdk\DirectRequestProvider')
            ->needs('$isDebug')
            ->give(env('APP_ENV')=='local');

        $this->app->when('ApiSdk\GatewayRequestProvider')
            ->needs('$endpoint')
            ->give(env('GATEWAY_API_URL'));

        $this->app->when('ApiSdk\GatewayRequestProvider')
            ->needs('$env')
            ->give(env('GATEWAY_API_ENV'));

        $this->app->when('ApiSdk\GatewayRequestProvider')
            ->needs('$app')
            ->give(env('APP_ENV')=='local');

        $this->app->when('ApiSdk\GatewayRequestProvider')
            ->needs('$token')
            ->give(env('GATEWAY_API_APP'));

        $this->app->when('ApiSdk\GatewayRequestProvider')
            ->needs('$isDebug')
            ->give(env('GATEWAY_API_APP_TOKEN'));
    }
}
