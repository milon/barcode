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
        return array("DNS1D", "DNS2D");
    }

}
