<?php
/**
 * Manages the base class for all package tests
 *
 * PHP version 7
 *
 * @category Test
 * @package  Adoxography\Disambiguatable
 * @author   Graham Still <gstill@uw.edu>
 * @license  MIT (https://github.com/adoxography/disambiguatable/blob/master/LICENSE)
 * @link     https://github.com/adoxography/disambiguatable
 */

namespace Adoxography\Disambiguatable\Tests;

use Illuminate\Foundation\Application;
use Adoxography\Disambiguatable\DisambiguatableServiceProvider;

/**
 * Defines the base class for tests in this package
 *
 * @category Test
 * @package  Adoxography\Disambiguatable
 * @author   Graham Still <gstill@uw.edu>
 * @license  MIT (https://github.com/adoxography/disambiguatable/blob/master/LICENSE)
 * @link     https://github.com/adoxography/disambiguatable
 */
class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Called before every test
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->artisan('migrate', ['--database' => 'testbench'])->run();
    }

    /**
     * Retrieves the service providers for the package
     *
     * @param \Illuminate\Foundation\Application $app A reference to the application
     *
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [DisambiguatableServiceProvider::class];
    }

    /**
     * Sets up the environment variables required for testing the package
     *
     * @param \Illuminate\Foundation\Application $app A reference to the application
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set(
            'database.connections.testbench',
            [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => ''
            ]
        );
    }
}
