<?php

namespace Milon\Barcode\Tests;

use Milon\Barcode\DNS1D;
use PHPUnit\Framework\TestCase;

class DNS1DTest extends TestCase
{
    /** @var DNS1D */
    private $dns1d;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dns1d = new DNS1D();
        $this->dns1d->setStorPath(sys_get_temp_dir());
    }

    public function testGetBarcodeSVGReturnsValidSvgForCode39(): void
    {
        $svg = $this->dns1d->getBarcodeSVG('TEST123', 'C39', 2, 30, 'black', true, false);

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('</svg>', $svg);
        $this->assertStringContainsString('xml version', $svg);
        $this->assertStringContainsString('TEST123', $svg);
        $this->assertStringContainsString('<rect', $svg);
    }

    public function testGetBarcodeSVGInlineOmitsXmlDeclaration(): void
    {
        $svg = $this->dns1d->getBarcodeSVG('ABC', 'C39', 2, 30, 'black', true, true);

        $this->assertStringNotContainsString('<?xml', $svg);
        $this->assertStringContainsString('<svg', $svg);
    }

    public function testGetBarcodeHTMLReturnsValidHtmlForCode39(): void
    {
        $html = $this->dns1d->getBarcodeHTML('TEST', 'C39', 2, 30, 'black', 0);

        $this->assertStringContainsString('<div', $html);
        $this->assertStringContainsString('position:relative', $html);
        $this->assertStringContainsString('style="', $html);
    }

    public function testGetBarcodeSVGForEAN13(): void
    {
        $svg = $this->dns1d->getBarcodeSVG('5901234123457', 'EAN13', 2, 30, 'black', true, false);

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('5901234123457', $svg);
    }

    public function testGetBarcodeSVGForC128(): void
    {
        $svg = $this->dns1d->getBarcodeSVG('Code128', 'C128', 2, 30, 'black', true, false);

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('Code128', $svg);
    }

    public function testSetStorPathReturnsThis(): void
    {
        $dns = new DNS1D();
        $result = $dns->setStorPath('/tmp');

        $this->assertSame($dns, $result);
    }
}
