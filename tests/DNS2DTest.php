<?php

namespace Milon\Barcode\Test;

use Milon\Barcode\DNS2D;

class DNS2DTest extends TestCase
{
    public function testGetBarcodeSvg()
    {
        $generator = new DNS2D();

        $svgContent = $generator->getBarcodeSVG('4445645656', 'QRCODE');

        $this->assertNotEmpty($svgContent);
        $this->assertStringContainsString('<?xml', $svgContent);
        $this->assertStringContainsString('<svg', $svgContent);
        $this->assertStringContainsString('</svg>', $svgContent);
    }

    public function testGetBarcodeHtml()
    {
        $generator = new DNS2D();

        $htmlContent = $generator->getBarcodeHTML('4445645656', 'QRCODE');

        $this->assertNotEmpty($htmlContent);
        $this->assertStringContainsString('<div', $htmlContent);
        $this->assertStringContainsString('</div>', $htmlContent);
    }

    public function testGetBarcodePng()
    {
        $generator = new DNS2D();

        $pngContent = $generator->getBarcodePNG('4445645656', 'QRCODE',3,33, [1, 1, 1]);

        $this->assertNotEmpty($pngContent);
    }

    public function testGetBarcodePngPath()
    {
        $generator = new DNS2D();
        $generator->setStorPath(__DIR__ . '/storage/');

        $pngContent = $generator->getBarcodePNGPath('4445645656', 'QRCODE',3,33, [1, 1, 1], true);
        $this->assertNotEmpty($pngContent);
        $this->assertFileExists($pngContent);
    }
}