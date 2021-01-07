<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // when APP_ENV is not local then set it to https true
        If (env('APP_ENV') !== 'local') {
          $this->app['request']->server->set('HTTPS', true);
        }
    }
}
