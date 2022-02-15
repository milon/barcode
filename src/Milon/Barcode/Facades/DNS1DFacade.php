<?php

namespace Milon\Barcode\Facades;

use Illuminate\Support\Facades\Facade;

class DNS1DFacade extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'DNS1D';
    }
}
