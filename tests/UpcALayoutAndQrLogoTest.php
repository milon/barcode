<?php

namespace Milon\Barcode\Tests;

use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class UpcALayoutAndQrLogoTest extends TestCase
{
    public function testUpcaEncoderUsesTallerGuardBars(): void
    {
        $dns = new DNS1D();
        $method = new ReflectionMethod(DNS1D::class, 'setBarcode');
        $method->invoke($dns, '042100005264', 'UPCA');

        $property = new \ReflectionProperty(DNS1D::class, 'barcode_array');
        $arr = $property->getValue($dns);

        $this->assertSame('eanupc', $arr['layout']);
        $this->assertSame('UPCA', $arr['ean_type']);
        $this->assertSame(11, $arr['maxh']);

        $heights = array();
        foreach ($arr['bcode'] as $bar) {
            if ($bar['t']) {
                $heights[$bar['h']] = true;
            }
        }
        $this->assertArrayHasKey(11, $heights);
        $this->assertArrayHasKey(7, $heights);
    }

    public function testUpcaPngWithShowCodeContainsSplitHri(): void
    {
        $dns = new DNS1D();
        $dns->setStorPath(sys_get_temp_dir());
        $png = $dns->getBarcodePNG('042100005264', 'UPCA', 2, 60, array(0, 0, 0), true, array(255, 255, 255));
        $raw = base64_decode($png, true);

        $this->assertNotFalse($raw);
        $this->assertSame("\x89PNG\r\n\x1a\n", substr($raw, 0, 8));

        $svg = $dns->getBarcodeSVG('042100005264', 'UPCA', 2, 60, 'black', true, true);
        $this->assertStringContainsString('>42100<', $svg);
        $this->assertStringContainsString('>00526<', $svg);
    }

    public function testEan13HriKeepsFirstDigitSeparate(): void
    {
        $dns = new DNS1D();
        $svg = $dns->getBarcodeSVG('5901234123457', 'EAN13', 2, 60, 'black', true, true);

        $this->assertStringContainsString('>5<', $svg);
        $this->assertStringContainsString('>901234<', $svg);
        $this->assertStringContainsString('>123457<', $svg);
    }

    public function testQrLogoIsCompositedInCenter(): void
    {
        $logoPath = sys_get_temp_dir() . '/milon-barcode-logo-' . uniqid('', true) . '.png';
        $logo = imagecreatetruecolor(40, 40);
        $red = imagecolorallocate($logo, 220, 20, 60);
        imagefilledrectangle($logo, 0, 0, 39, 39, $red);
        imagepng($logo, $logoPath);

        $dns = new DNS2D();
        $dns->setStorPath(sys_get_temp_dir());
        $dns->setLogo($logoPath, 0.25);

        $png = $dns->getBarcodePNG(
            'https://example.com/logo-qr',
            'QRCODE,H',
            4,
            4,
            array(0, 0, 0),
            array(255, 255, 255)
        );

        $image = imagecreatefromstring(base64_decode($png));
        $this->assertNotFalse($image);

        $w = imagesx($image);
        $h = imagesy($image);
        $center = imagecolorat($image, (int) ($w / 2), (int) ($h / 2));
        $rgb = imagecolorsforindex($image, $center);

        // Center should be dominated by the red logo, not QR black/white modules alone.
        $this->assertGreaterThan(150, $rgb['red']);
        $this->assertLessThan(80, $rgb['green']);

        unlink($logoPath);
        $dns->setLogo(null);
    }

    public function testSetLogoIsFluent(): void
    {
        $dns = new DNS2D();
        $this->assertSame($dns, $dns->setLogo('/tmp/x.png', 0.15));
        $this->assertSame('/tmp/x.png', $dns->getLogoPath());
        $dns->setLogo(null);
        $this->assertNull($dns->getLogoPath());
    }

    public function testUpcaIncludesLeadingZeroDigit(): void
    {
        $dns = new DNS1D();
        $svg = $dns->getBarcodeSVG('042100005264', 'UPCA', 2, 60, 'black', true, true);

        $this->assertStringContainsString('>0<', $svg);
        $this->assertStringContainsString('>42100<', $svg);
        $this->assertStringContainsString('>00526<', $svg);
        $this->assertStringContainsString('>4<', $svg);
    }
}
