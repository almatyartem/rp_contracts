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
        $this->app->when(GatewayApi::class)
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
            ->give(env('APP_ENV') == 'local');

        $this->app->when(StructureApi::class)
            ->needs('$structure')
            ->give(function(){
                return json_decode(file_get_contents(base_path('config/structure.json')), true);
            });

        $this->app->bind('coreapi',function(){
            return $this->app->make(CoreApi::class);
        });

        $this->app->bind('structureapi',function(){
            return $this->app->make(StructureApi::class);
        });
    }
}
