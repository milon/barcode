<?php

namespace Milon\Barcode\Tests;

use Milon\Barcode\DNS2D;
use PHPUnit\Framework\TestCase;

class DNS2DTest extends TestCase
{
    /** @var DNS2D */
    private $dns2d;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dns2d = new DNS2D();
        $this->dns2d->setStorPath(sys_get_temp_dir());
    }

    public function testGetBarcodeSVGReturnsValidSvgForQrCode(): void
    {
        $svg = $this->dns2d->getBarcodeSVG('https://example.com', 'QRCODE', 3, 3, 'black');

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('</svg>', $svg);
        $this->assertStringContainsString('xml version', $svg);
        $this->assertStringContainsString('<rect', $svg);
    }

    public function testGetBarcodeSVGForDatamatrix(): void
    {
        $svg = $this->dns2d->getBarcodeSVG('Datamatrix', 'DATAMATRIX', 3, 3, 'black');

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('</svg>', $svg);
    }

    public function testGetBarcodeSVGForPdf417(): void
    {
        $svg = $this->dns2d->getBarcodeSVG('PDF417', 'PDF417', 3, 3, 'black');

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('</svg>', $svg);
    }

    public function testGetBarcodeHTMLReturnsValidHtmlForQrCode(): void
    {
        $html = $this->dns2d->getBarcodeHTML('QR', 'QRCODE', 10, 10, 'black');

        $this->assertStringContainsString('<div', $html);
        $this->assertStringContainsString('position:relative', $html);
    }

    public function testSetStorPathReturnsThis(): void
    {
        $dns = new DNS2D();
        $result = $dns->setStorPath('/tmp');

        $this->assertSame($dns, $result);
    }
}
