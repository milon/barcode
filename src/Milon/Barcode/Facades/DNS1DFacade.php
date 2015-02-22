<?php namespace Milon\Barcode\Facades;

use Illuminate\Support\Facades\Facade;

class DNS1DFacade extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'DNS1D';
    }

}
