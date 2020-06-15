<?php
/**
 * Contains the service provider for this package
 *
 * PHP version 7
 *
 * @category ServiceProvider
 * @package  Adoxography\Disambiguatable
 * @author   Graham Still <gstill@uw.edu>
 * @license  MIT (https://github.com/adoxography/disambiguatable/blob/master/LICENSE)
 * @link     https://github.com/adoxography/disambiguatable
 */

namespace Adoxography\Disambiguatable;

use Illuminate\Support\ServiceProvider;

/**
 * Defines the service provider for this package
 *
 * @category ServiceProvider
 * @package  Adoxography\Disambiguatable
 * @author   Graham Still <gstill@uw.edu>
 * @license  MIT (https://github.com/adoxography/disambiguatable/blob/master/LICENSE)
 * @link     https://github.com/adoxography/disambiguatable
 */
class DisambiguatableServiceProvider extends ServiceProvider
{
    /**
     * Boots the service provider
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
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
        //
    }
}
