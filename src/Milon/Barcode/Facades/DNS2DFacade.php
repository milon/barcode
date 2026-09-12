<?php

namespace Milon\Barcode\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string getBarcodeSVG(string $code, string $type, int $w = 3, int $h = 3, string $color = 'black')
 * @method static string getBarcodeHTML(string $code, string $type, int $w = 10, int $h = 10, string $color = 'black')
 * @method static string|false getBarcodePNG(string $code, string $type, int $w = 3, int $h = 3, array $color = [0, 0, 0], ?array $bgcolor = null)
 * @method static string|false getBarcodePNGPath(string $code, string $type, int $w = 3, int $h = 3, array $color = [0, 0, 0], ?array $bgcolor = null, ?string $filename = null)
 * @method static \Milon\Barcode\DNS2D setStorPath(string $path)
 * @method static \Milon\Barcode\DNS2D setPadding(int $padding)
 * @method static int getPadding()
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
