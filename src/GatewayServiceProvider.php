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
        $this->app->singleton('ApiSdk\GatewayApi', function ($app) {
            return new GatewayApi($app->make(GuzzleHttp\Client::class), env('GATEWAY_API_URL'), env('GATEWAY_API_ENV'), env('GATEWAY_API_APP'),
                env('GATEWAY_API_APP_TOKEN'), env('APP_ENV')=='local');
        });

        $this->app->singleton('ApiSdk\AuthApi', function ($app) {
            return new AuthApi($app->make(GatewayApi::class), env('AUTH_API_CLIENT_ID'), env('AUTH_API_CLIENT_SECRET'), env('APP_URL').'/oauth_callback');
        });

        $this->app->singleton('ApiSdk\StructureApi', function ($app) {
            return new StructureApi(json_decode(file_get_contents(base_path('config/structure.json')), true));
        });

        $this->app->bind('coreapi',function(){
            return $this->app->make(CoreApi::class);
        });

        $this->app->bind('structureapi',function(){
            return $this->app->make(StructureApi::class);
        });
    }
}
