<?php

namespace Milon\Barcode\Test;

use Milon\Barcode\DNS1D;

class DNS1DTest extends TestCase
{
    public function testGetBarcodeSvg()
    {
        $generator = new DNS1D();

        $svgContent = $generator->getBarcodeSVG('4445645656', 'PHARMA2T',3,33,'green');

        $this->assertNotEmpty($svgContent);
        $this->assertStringContainsString('<?xml', $svgContent);
        $this->assertStringContainsString('<svg', $svgContent);
        $this->assertStringContainsString('</svg>', $svgContent);
        $this->assertStringContainsString('fill="green"', $svgContent);
    }

    public function testGetBarcodeHtml()
    {
        $generator = new DNS1D();

        $htmlContent = $generator->getBarcodeHTML('4445645656', 'PHARMA2T',3,33,'green');

        $this->assertNotEmpty($htmlContent);
        $this->assertStringContainsString('<div', $htmlContent);
        $this->assertStringContainsString('</div>', $htmlContent);
        $this->assertStringContainsString('color:green', $htmlContent);
    }

    public function testGetBarcodePng()
    {
        $generator = new DNS1D();

        $pngContent = $generator->getBarcodePNG('4445645656', 'C39+',3,33, [1, 1, 1]);

        $this->assertNotEmpty($pngContent);
    }

    public function testGetBarcodePngPath()
    {
        $generator = new DNS1D();
        $generator->setStorPath(__DIR__ . '/storage/');

        $pngContent = $generator->getBarcodePNGPath('4445645656', 'C39+',3,33, [1, 1, 1], true);
        $this->assertNotEmpty($pngContent);
        $this->assertFileExists($pngContent);
    }
}