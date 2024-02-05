<?php

namespace Milon\Barcode\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string getBarcodeSVG(string $code, string $type, int $w = 3, int $h = 3, string $color = 'black')
 * @method static string getBarcodeHTML(string $code, string $type, int $w = 10, int $h = 10, string $color = 'black')
 * @method static string|false getBarcodePNG(string $code, string $type, int $w = 3, int $h = 3, array $color = [0, 0, 0],)
 * @method static string|false getBarcodePNGPath(string $code, string $type, int $w = 2, int $h = 30, array $color = [0, 0, 0])
 * @method static \Milon\Barcode\DNS2D setStorPath(string $path)
 */
class DNS2DFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'DNS2D';
    }

}
