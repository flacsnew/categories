<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Library\Services\SmsAero;
use App\Library\Services\LocalSender;
use App;

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
        if (App::environment('local')) {
            $this->app->bind(
                'App\Library\Services\Contracts\SmsSenderServiceInterface',
                'App\Library\Services\SmsAero'
            );
        } elseif (App::environment('testing')) {
            $this->app->bind(
                'App\Library\Services\Contracts\SmsSenderServiceInterface',
                'App\Library\Services\LocalSender'
            );
        }

        $this->app->bind('\GuzzleHttp\Client', function ($app) {
            return new \GuzzleHttp\Client();
        });
    }
}
