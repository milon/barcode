<?php 

namespace Milon\Barcode;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class BarcodeServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Publish asset
     */
    public function boot() {
        $this->publishes([
              __DIR__.'/../../config/config.php' => config_path('barcode.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {

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
    public function provides() {
        return ['DNS1D', 'DNS2D'];
    }
}
