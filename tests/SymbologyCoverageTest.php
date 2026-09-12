<?php

namespace Milon\Barcode\Tests;

use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use Milon\Barcode\InvalidBarcodeException;
use PHPUnit\Framework\TestCase;

/**
 * Broader symbology / capacity / PNG coverage beyond the original smoke tests.
 */
class SymbologyCoverageTest extends TestCase
{
    /** @var DNS1D */
    private $dns1d;

    /** @var DNS2D */
    private $dns2d;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dns1d = new DNS1D();
        $this->dns1d->setStorPath(sys_get_temp_dir());
        $this->dns2d = new DNS2D();
        $this->dns2d->setStorPath(sys_get_temp_dir());
    }

    public function testC128AutoEncodesMixedCase(): void
    {
        $svg = $this->dns1d->getBarcodeSVG('Code128Demo', 'C128');
        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('Code128Demo', $svg);
    }

    public function testC128AEncodesUppercase(): void
    {
        $svg = $this->dns1d->getBarcodeSVG('CODE128A', 'C128A');
        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('CODE128A', $svg);
    }

    public function testC128BEncodesMixedCase(): void
    {
        $svg = $this->dns1d->getBarcodeSVG('Code128B', 'C128B');
        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('Code128B', $svg);
    }

    public function testC128CEncodesEvenDigitPairs(): void
    {
        $svg = $this->dns1d->getBarcodeSVG('12345678', 'C128C');
        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('12345678', $svg);
    }

    public function testC128VariantsProduceValidPng(): void
    {
        $cases = array(
            array('Code128Demo', 'C128'),
            array('CODE128A', 'C128A'),
            array('Code128B', 'C128B'),
            array('12345678', 'C128C'),
        );

        foreach ($cases as $case) {
            list($code, $type) = $case;
            $png = $this->dns1d->getBarcodePNG($code, $type, 2, 40, array(0, 0, 0), true, array(255, 255, 255));
            $raw = base64_decode($png, true);
            $this->assertNotFalse($raw, 'Invalid base64 for ' . $type);
            $this->assertSame("\x89PNG\r\n\x1a\n", substr($raw, 0, 8), 'Corrupt PNG for ' . $type);
        }
    }

    public function testInvalidI25ThrowsNumericHint(): void
    {
        $this->expectException(InvalidBarcodeException::class);
        $this->expectExceptionMessage('requires a numeric code');

        $this->dns1d->getBarcodeSVG('ABC', 'I25');
    }

    public function testInvalidUpcaThrowsNumericHint(): void
    {
        $this->expectException(InvalidBarcodeException::class);
        $this->expectExceptionMessage('requires a numeric code');

        $this->dns1d->getBarcodeSVG('NOTADIGIT', 'UPCA');
    }

    public function testInvalidEan8ThrowsNumericHint(): void
    {
        $this->expectException(InvalidBarcodeException::class);
        $this->expectExceptionMessage('requires a numeric code');

        $this->dns1d->getBarcodeSVG('ABC', 'EAN8');
    }

    public function testInvalidS25ThrowsNumericHint(): void
    {
        $this->expectException(InvalidBarcodeException::class);
        $this->expectExceptionMessage('requires a numeric code');

        $this->dns1d->getBarcodeSVG('ABC', 'S25');
    }

    public function testEmptyQrCodeThrowsCapacityHint(): void
    {
        $this->expectException(InvalidBarcodeException::class);
        $this->expectExceptionMessage('empty or exceed the maximum capacity');

        $this->dns2d->getBarcodeSVG('', 'QRCODE');
    }

    public function testOversizedQrCodeThrowsCapacityHint(): void
    {
        // Binary/byte mode QR max is 2953; far larger payloads must fail loudly.
        $payload = str_repeat('A', 7089);

        $this->expectException(InvalidBarcodeException::class);
        $this->expectExceptionMessage('empty or exceed the maximum capacity');

        $this->dns2d->getBarcodeSVG($payload, 'QRCODE');
    }

    public function testOversizedQrCodeFailsForPngAsWell(): void
    {
        $payload = str_repeat('Q', 10000);

        $this->expectException(InvalidBarcodeException::class);

        $this->dns2d->getBarcodePNG($payload, 'QRCODE', 2, 2);
    }

    public function testLargeButValidQrCodeStillEncodes(): void
    {
        $payload = str_repeat('B', 1000);
        $svg = $this->dns2d->getBarcodeSVG($payload, 'QRCODE', 2, 2);

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('<rect', $svg);
    }

    public function testDns2dAcceptsCustomBackgroundColor(): void
    {
        $png = $this->dns2d->getBarcodePNG(
            'https://example.com/bg',
            'QRCODE',
            3,
            3,
            array(0, 0, 0),
            array(200, 220, 240)
        );

        $image = imagecreatefromstring(base64_decode($png));
        $this->assertNotFalse($image);
        $this->assertSame(-1, imagecolortransparent($image));

        $colors = imagecolorsforindex($image, 0);
        $this->assertSame(200, $colors['red']);
        $this->assertSame(220, $colors['green']);
        $this->assertSame(240, $colors['blue']);
    }

    public function testDns1dShowCodePngKeepsValidHeader(): void
    {
        // Odd module widths used to emit float→int deprecations into the PNG buffer.
        $png = $this->dns1d->getBarcodePNG(
            'Code128Demo',
            'C128',
            3,
            80,
            array(0, 0, 0),
            true,
            array(255, 255, 255)
        );

        $raw = base64_decode($png, true);
        $this->assertNotFalse($raw);
        $this->assertSame("\x89PNG\r\n\x1a\n", substr($raw, 0, 8));
        $this->assertNotFalse(imagecreatefromstring($raw));
    }

    public function testDns1dPngPathHonorsBackgroundColor(): void
    {
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'milon-barcode-bg-' . uniqid('', true);
        mkdir($dir);
        $this->dns1d->setStorPath($dir);

        try {
            $path = $this->dns1d->getBarcodePNGPath(
                'BGTEST',
                'C39',
                2,
                30,
                array(0, 0, 0),
                false,
                array(255, 240, 200),
                'bg-test'
            );
            $absolute = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR)
                . DIRECTORY_SEPARATOR
                . ltrim($path, '/\\');

            $this->assertFileExists($absolute);
            $image = imagecreatefrompng($absolute);
            $this->assertNotFalse($image);
            $this->assertSame(-1, imagecolortransparent($image));
            $colors = imagecolorsforindex($image, 0);
            $this->assertSame(255, $colors['red']);
            $this->assertSame(240, $colors['green']);
            $this->assertSame(200, $colors['blue']);
            unlink($absolute);
        } finally {
            if (is_dir($dir)) {
                foreach (glob($dir . DIRECTORY_SEPARATOR . '*') ?: array() as $file) {
                    unlink($file);
                }
                rmdir($dir);
            }
        }
    }

    public function testFactoryMessagesAreStable(): void
    {
        $this->assertStringContainsString(
            'lowercase letters are not allowed',
            InvalidBarcodeException::encodingFailureMessage('C128A', 'abc')
        );
        $this->assertStringContainsString(
            'even number of digits',
            InvalidBarcodeException::encodingFailureMessage('C128C', '123')
        );
        $this->assertStringContainsString(
            'maximum capacity',
            InvalidBarcodeException::encodingFailureMessage('QRCODE', str_repeat('Z', 9999))
        );
        $this->assertStringContainsString(
            'Unsupported barcode type: FOO',
            InvalidBarcodeException::forUnsupportedType('FOO')->getMessage()
        );
    }
}
