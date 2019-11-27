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
        $this->app->singleton(GatewayApi::class, function ($app)
        {
            return new GatewayApi($app->make(Client::class), env('GATEWAY_API_URL'), env('GATEWAY_API_ENV'),
                env('GATEWAY_API_CLIENT_ID'), env('GATEWAY_API_CLIENT_SECRET'), env('APP_ENV') == 'local');
        });

        /*$this->app->when(GatewayApi::class)
            ->needs('$endpoint')
            ->give(env('GATEWAY_API_URL'));

        $this->app->when(GatewayApi::class)
            ->needs('$env')
            ->give(env('GATEWAY_API_ENV'));

        $this->app->when(GatewayApi::class)
            ->needs('$clientId')
            ->give(env('GATEWAY_API_CLIENT_ID'));

        $this->app->when(GatewayApi::class)
            ->needs('$clientSecret')
            ->give(env('GATEWAY_API_CLIENT_SECRET'));

        $this->app->when(GatewayApi::class)
            ->needs('$isDebug')
            ->give(env('APP_ENV') == 'local');*/
    }
}
