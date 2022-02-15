<?php

namespace Milon\Barcode\Facades;

use Illuminate\Support\Facades\Facade;

class DNS2DFacade extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'DNS2D';
    }
}
