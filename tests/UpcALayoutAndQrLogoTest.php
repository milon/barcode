<?php

namespace Milon\Barcode\Tests;

use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use PHPUnit\Framework\TestCase;

class UpcALayoutAndQrLogoTest extends TestCase
{
    public function testUpcaEncoderUsesTallerGuardBars(): void
    {
        $dns = new DNS1D();
        // Public SVG output exposes relative bar heights (guards full, digits shorter).
        $svg = $dns->getBarcodeSVG('042100005264', 'UPCA', 2, 55, 'black', false, true);

        $this->assertStringContainsString('height="55"', $svg);
        // digitH/maxh = 7/11 of 55 ≈ 35.000
        $this->assertMatchesRegularExpression('/height="35(\.0+)?"/', $svg);
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

    public function testEan13HtmlRetailLayoutKeepsFullHeightAndSplitHri(): void
    {
        $dns = new DNS1D();
        $html = $dns->getBarcodeHTML('5901234123457', 'EAN13', 2, 60, 'black', 14);

        // Digit bars keep 7/11 of the height instead of subtracting showCode a second time.
        $this->assertStringContainsString('height:38.182px', $html);
        // Guard bars stay at full height and the root box does not grow.
        $this->assertStringContainsString('height:60px', $html);
        // Split human-readable digits, like the SVG/PNG output.
        $this->assertStringContainsString('>5</div>', $html);
        $this->assertStringContainsString('>901234</div>', $html);
        $this->assertStringContainsString('>123457</div>', $html);
    }

    public function testEan13HtmlWithoutShowCodeHasNoDigits(): void
    {
        $dns = new DNS1D();
        $html = $dns->getBarcodeHTML('5901234123457', 'EAN13', 2, 55);

        $this->assertStringNotContainsString('text-align:center;color', $html);
    }

    public function testEan13HtmlShowCodeTrueUsesSvgDefaultFontSize(): void
    {
        $dns = new DNS1D();
        $html = $dns->getBarcodeHTML('5901234123457', 'EAN13', 2, 60, 'black', true);

        $this->assertStringContainsString('font-size:12px', $html);
    }

    public function testNonEanHtmlStillReservesSpaceForCode(): void
    {
        $dns = new DNS1D();
        $html = $dns->getBarcodeHTML('TEST123', 'C128', 2, 50, 'black', 12);

        // 50 - (12 + 12) = 26px, same as before the retail layout.
        $this->assertStringContainsString('height:26px', $html);
        $this->assertStringContainsString('font-size: 12px;">TEST123</div>', $html);
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
