<?php 

namespace Milon\Barcode;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class BarcodeServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Publish asset
     */
    public function boot()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
              __DIR__ . '/../../config/config.php' => $this->app->make('path.config') . DIRECTORY_SEPARATOR . 'barcode.php',
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('DNS1D', function() {
            return new DNS1D;
        });
        $this->app->singleton('DNS2D', function() {
            return new DNS2D;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['DNS1D', 'DNS2D'];
    }
}
