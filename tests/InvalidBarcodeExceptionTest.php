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

    public function testDns1dThrowsCharsetHintForC128CWithLetters(): void
    {
        $dns = new DNS1D();
        $dns->setStorPath(sys_get_temp_dir());

        $this->expectException(InvalidBarcodeException::class);
        $this->expectExceptionMessage('C128C supports digits only');

        $dns->getBarcodeSVG('ABC', 'C128C');
    }

    public function testDns1dThrowsEvenLengthHintForC128C(): void
    {
        $dns = new DNS1D();
        $dns->setStorPath(sys_get_temp_dir());

        $this->expectException(InvalidBarcodeException::class);
        $this->expectExceptionMessage('C128C requires an even number of digits');

        $dns->getBarcodeSVG('123', 'C128C');
    }

    public function testDns1dThrowsCharsetHintForC128ALowercase(): void
    {
        $dns = new DNS1D();
        $dns->setStorPath(sys_get_temp_dir());

        $this->expectException(InvalidBarcodeException::class);
        $this->expectExceptionMessage('lowercase letters are not allowed');

        $dns->getBarcodeSVG('abc', 'C128A');
    }

    public function testDns1dThrowsNumericHintForEan13(): void
    {
        $dns = new DNS1D();
        $dns->setStorPath(sys_get_temp_dir());

        $this->expectException(InvalidBarcodeException::class);
        $this->expectExceptionMessage('requires a numeric code');

        $dns->getBarcodeSVG('MT-00001', 'EAN13');
    }

    public function testDns1dThrowsCode39CharsetHint(): void
    {
        $dns = new DNS1D();
        $dns->setStorPath(sys_get_temp_dir());

        $this->expectException(InvalidBarcodeException::class);
        $this->expectExceptionMessage('CODE 39 supports digits, uppercase letters');

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

    public function testDns2dThrowsCapacityHintForEmptyDatamatrix(): void
    {
        $dns = new DNS2D();
        $dns->setStorPath(sys_get_temp_dir());

        $this->expectException(InvalidBarcodeException::class);
        $this->expectExceptionMessage('empty or exceed the maximum capacity');

        $dns->getBarcodeSVG('', 'DATAMATRIX');
    }
}
