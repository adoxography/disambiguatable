<?php

namespace Adoxography\Disambiguatable;

use Illuminate\Support\ServiceProvider;

class DisambiguatableServiceProvider extends ServiceProvider
{
    /**
     * Publishes the configuration file
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('disambiguatable.php')
            ], 'config');

            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }

    /**
     * Make config publishment optional by merging the config from the package
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/config.php',
            'disambiguatable'
        );
    }
}
