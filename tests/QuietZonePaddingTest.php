<?php

namespace Milon\Barcode\Tests;

use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use PHPUnit\Framework\TestCase;

class QuietZonePaddingTest extends TestCase
{
    public function testDefaultPaddingIsZero(): void
    {
        $dns = new DNS1D();
        $this->assertSame(0, $dns->getPadding());
    }

    public function testSetPaddingIsFluentAndClamped(): void
    {
        $dns = new DNS1D();
        $this->assertSame($dns, $dns->setPadding(12));
        $this->assertSame(12, $dns->getPadding());

        $dns->setPadding(-5);
        $this->assertSame(0, $dns->getPadding());
    }

    public function testDns1dPngGrowsByTwicePadding(): void
    {
        $dns = new DNS1D();
        $dns->setStorPath(sys_get_temp_dir());

        $plain = $this->imageSize($dns->getBarcodePNG('PADTEST', 'C39', 2, 40, array(0, 0, 0), false, array(255, 255, 255)));

        $dns->setPadding(10);
        $padded = $this->imageSize($dns->getBarcodePNG('PADTEST', 'C39', 2, 40, array(0, 0, 0), false, array(255, 255, 255)));

        $this->assertSame($plain['w'] + 20, $padded['w']);
        $this->assertSame($plain['h'] + 20, $padded['h']);
    }

    public function testDns1dSvgIncludesPaddingAndCrispEdges(): void
    {
        $dns = new DNS1D();
        $dns->setPadding(8);
        $svg = $dns->getBarcodeSVG('PAD', 'C39', 2, 30, 'black', false, true);

        $this->assertStringContainsString('shape-rendering="crispEdges"', $svg);
        $this->assertMatchesRegularExpression('/width="[0-9.]+"/', $svg);
        $this->assertStringContainsString('x="8"', $svg);
    }

    public function testDns2dPngQuietZone(): void
    {
        $dns = new DNS2D();
        $dns->setStorPath(sys_get_temp_dir());

        $plain = $this->imageSize($dns->getBarcodePNG('https://example.com/pad', 'QRCODE', 3, 3, array(0, 0, 0), array(255, 255, 255)));

        $dns->setPadding(4);
        $padded = $this->imageSize($dns->getBarcodePNG('https://example.com/pad', 'QRCODE', 3, 3, array(0, 0, 0), array(255, 255, 255)));

        $this->assertSame($plain['w'] + 8, $padded['w']);
        $this->assertSame($plain['h'] + 8, $padded['h']);
    }

    public function testDns1dHtmlAppliesPaddingOffset(): void
    {
        $dns = new DNS1D();
        $dns->setPadding(6);
        $html = $dns->getBarcodeHTML('HTMLPAD', 'C39', 2, 30, 'black', 0);

        $this->assertStringContainsString('left:6px', $html);
        $this->assertStringContainsString('top:6px', $html);
    }

    /**
     * @return array{w:int,h:int}
     */
    private function imageSize(string $base64Png): array
    {
        $image = imagecreatefromstring(base64_decode($base64Png));
        $this->assertNotFalse($image);

        return array(
            'w' => imagesx($image),
            'h' => imagesy($image),
        );
    }
}
