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
            ->give(function(){
                return env('GATEWAY_API_URL');
            });

        $this->app->when(GatewayApi::class)
            ->needs('$env')
            ->give(function(){
                return env('GATEWAY_API_ENV');
            });

        $this->app->when(GatewayApi::class)
            ->needs('$app')
            ->give(function(){
                return env('GATEWAY_API_APP');
            });

        $this->app->when(GatewayApi::class)
            ->needs('$token')
            ->give(function(){
                return env('GATEWAY_API_APP_TOKEN');
            });

        $this->app->when(GatewayApi::class)
            ->needs('$isDebug')
            ->give(function(){
                return env('APP_ENV');
            });

        $this->app->when(StructureApi::class)
            ->needs('$structure')
            ->give(function(){
                return json_decode(file_get_contents(base_path('config/structure.json')), true);
            });

        $this->app->when(AuthApi::class)
            ->needs('$clientId')
            ->give(function(){
                return env('AUTH_API_CLIENT_ID');
            });

        $this->app->when(AuthApi::class)
            ->needs('$clientSecret')
            ->give(function(){
                return env('AUTH_API_CLIENT_SECRET');
            });

        $this->app->when(AuthApi::class)
            ->needs('$oauthCallback')
            ->give(function(){
                return env('APP_URL').'/oauth_callback';
            });


        $this->app->bind('coreapi',function(){
            return $this->app->make(CoreApi::class);
        });

        $this->app->bind('structureapi',function(){
            return $this->app->make(StructureApi::class);
        });
    }
}
