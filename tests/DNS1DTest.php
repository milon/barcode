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

    public function testCode11KCheckDigitTenIsADash(): void
    {
        $this->assertSame('S0000000000113S', $this->code11Symbols('00000000001'));
        $this->assertSame('S000000000077-S', $this->code11Symbols('00000000007'));
    }

    private function code11Symbols(string $code): string
    {
        $method = new \ReflectionMethod(DNS1D::class, 'barcode_code11');
        if (PHP_VERSION_ID < 80100) {
            $method->setAccessible(true);
        }
        $bars = $method->invoke($this->dns1d, $code);
        $map = array(
            '111121' => '0',
            '211121' => '1',
            '121121' => '2',
            '221111' => '3',
            '112121' => '4',
            '212111' => '5',
            '122111' => '6',
            '111221' => '7',
            '211211' => '8',
            '211111' => '9',
            '112111' => '-',
            '112211' => 'S',
        );
        $widths = array();
        foreach ($bars['bcode'] as $element) {
            $widths[] = (string) $element['w'];
        }
        $symbols = '';
        $count = count($widths);
        for ($i = 0; $i < $count; $i += 6) {
            $key = implode('', array_slice($widths, $i, 6));
            $symbols .= isset($map[$key]) ? $map[$key] : '?';
        }

        return $symbols;
    }
}
