<?php

namespace Milon\Barcode\Tests;

use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use Milon\Barcode\InvalidBarcodeException;
use PHPUnit\Framework\TestCase;

class InvalidBarcodeExceptionTest extends TestCase
{
    public function testDns1dThrowsForUnsupportedType(): void
    {
        $dns = new DNS1D();
        $dns->setStorPath(sys_get_temp_dir());

        $this->expectException(InvalidBarcodeException::class);
        $this->expectExceptionMessage('Unsupported barcode type');

        $dns->getBarcodeSVG('123', 'NOTATYPE');
    }

    public function testDns1dThrowsWhenEncodingFailsForCharset(): void
    {
        $dns = new DNS1D();
        $dns->setStorPath(sys_get_temp_dir());

        $this->expectException(InvalidBarcodeException::class);
        $this->expectExceptionMessage('Unable to encode barcode of type C128C');

        // C128C only supports digits; letters cannot be encoded
        $dns->getBarcodeSVG('ABC', 'C128C');
    }

    public function testDns1dThrowsForInvalidCode39Character(): void
    {
        $dns = new DNS1D();
        $dns->setStorPath(sys_get_temp_dir());

        $this->expectException(InvalidBarcodeException::class);
        $this->expectExceptionMessage('Unable to encode barcode of type C39');

        $dns->getBarcodeSVG('hello!', 'C39');
    }

    public function testDns2dThrowsForUnsupportedType(): void
    {
        $dns = new DNS2D();
        $dns->setStorPath(sys_get_temp_dir());

        $this->expectException(InvalidBarcodeException::class);
        $this->expectExceptionMessage('Unsupported barcode type');

        $dns->getBarcodeSVG('test', 'NOTATYPE');
    }

    public function testDns2dThrowsWhenDatamatrixEncodingFails(): void
    {
        $dns = new DNS2D();
        $dns->setStorPath(sys_get_temp_dir());

        $this->expectException(InvalidBarcodeException::class);
        $this->expectExceptionMessage('Unable to encode barcode of type DATAMATRIX');

        $dns->getBarcodeSVG('', 'DATAMATRIX');
    }
}
