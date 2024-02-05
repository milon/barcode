<?php 

namespace Milon\Barcode\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string getBarcodeSVG(string $code, string $type, int|float $w = 2, int|float $h = 30, string $color = 'black', bool $showCode = true, bool $inline = false)
 * @method static string getBarcodeHTML(string $code, string $type, int|float $w = 2, int|float $h = 30, string $color = 'black', bool $showCode = false)
 * @method static string|false getBarcodePNG(string $code, string $type, int|float $w = 2, int|float $h = 30, array $color = [0, 0, 0], bool $showCode = false)
 * @method static string|false getBarcodePNGPath(string $code, string $type, int|float $w = 2, int|float $h = 30, array $color = [0, 0, 0], bool $showCode = false)
 * @method static \Illuminate\Contracts\Routing\UrlGenerator|string getBarcodePNGUri(string $code, string $type, int|float $w = 2, int|float $h = 30, array $color = [0, 0, 0])
 */
class DNS1DFacade extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() 
    {
        return 'DNS1D';
    }

}
