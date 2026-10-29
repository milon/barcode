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
        $this->assertStringContainsString('>5<', $svg);
        $this->assertStringContainsString('>901234<', $svg);
        $this->assertStringContainsString('>123457<', $svg);
        // Guard bars taller than digit bars
        $this->assertStringContainsString('height="30"', $svg);
        $this->assertMatchesRegularExpression('/height="19\.0/', $svg);
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

    public function testStandard25DigitEightIsNotEncodedAsZero(): void
    {
        $zero = $this->standard25Widths('00');
        $eight = $this->standard25Widths('80');

        $this->assertNotSame($zero, $eight);
        $this->assertSame(array(1, 1, 1, 1, 3, 1, 3, 1, 1, 1), array_slice($zero, 6, 10));
        $this->assertSame(array(3, 1, 1, 1, 1, 1, 3, 1, 1, 1), array_slice($eight, 6, 10));
    }

    /**
     * @return int[]
     */
    private function standard25Widths(string $code): array
    {
        $method = new \ReflectionMethod(DNS1D::class, 'barcode_s25');
        if (PHP_VERSION_ID < 80100) {
            $method->setAccessible(true);
        }
        $bars = $method->invoke($this->dns1d, $code, false);
        $widths = array();
        foreach ($bars['bcode'] as $element) {
            $widths[] = $element['w'];
        }

        return $widths;
    }
}
