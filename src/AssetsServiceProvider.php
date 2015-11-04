<?php

namespace KodiCMS\Assets;

use Illuminate\Support\ServiceProvider;
use KodiCMS\Assets\Console\Commands\PackagesListCommand;

class AssetsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('assets', function ($app) {
            return new Assets();
        });

        $this->app->singleton('assets.packages', function ($app) {
            return new PackageManager();
        });

        $this->app->singleton('assets.meta', function ($app) {
            return new Meta();
        });

        $this->commands(PackagesListCommand::class);
    }
}
