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
        $this->publishes([
            __DIR__ . '/../config/disambiguatable.php' => config_path('disambiguatable.php')
        ], 'disambiguatable-config');
    }

    /**
     * Make config publishment optional by merging the config from the package
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '../config/disambiguatable.php',
            'disambiguatable'
        );
    }
}
