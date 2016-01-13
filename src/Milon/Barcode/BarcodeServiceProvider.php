<?php

namespace Milon\Barcode;

use Illuminate\Support\ServiceProvider;

class BarcodeServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;


/**
 * Publish asset
 */
	public function boot()
    {
		$this->package('milon/barcode');
	}

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['DNS1D'] = $this->app->share(function($app) {
            return new DNS1D;
        });

        $this->app['DNS2D'] = $this->app->share(function($app) {
            return new DNS2D;
        });

        // Shortcut so developers don't need to add an Alias in app/config/app.php
        $this->app->booting(function() {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('DNS1D', 'Milon\Barcode\Facades\DNS1DFacade');
        });

        $this->app->booting(function() {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('DNS2D', 'Milon\Barcode\Facades\DNS2DFacade');
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array("DNS1D", "DNS2D");
    }

}
