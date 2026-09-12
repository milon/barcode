<?php

namespace Milon\Barcode\Tests;

use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use PHPUnit\Framework\TestCase;

class PngBackgroundColorTest extends TestCase
{
    public function testDns2dDefaultBackgroundIsTransparent(): void
    {
        $dns = new DNS2D();
        $png = $dns->getBarcodePNG('https://example.com', 'QRCODE', 3, 3, array(0, 0, 0));

        $image = $this->createImage($png);
        $this->assertGreaterThanOrEqual(0, imagecolortransparent($image));
    }

    public function testDns2dAcceptsSolidBackgroundColor(): void
    {
        $dns = new DNS2D();
        $png = $dns->getBarcodePNG(
            'https://example.com',
            'QRCODE',
            3,
            3,
            array(0, 0, 0),
            array(255, 255, 255)
        );

        $image = $this->createImage($png);
        $this->assertSame(-1, imagecolortransparent($image));
        $this->assertBackgroundColor($image, 255, 255, 255);
    }

    public function testDns1dDefaultBackgroundIsTransparent(): void
    {
        $dns = new DNS1D();
        $png = $dns->getBarcodePNG('TEST123', 'C39', 2, 30, array(0, 0, 0), false);

        $image = $this->createImage($png);
        $this->assertGreaterThanOrEqual(0, imagecolortransparent($image));
    }

    public function testDns1dAcceptsSolidBackgroundColor(): void
    {
        $dns = new DNS1D();
        $png = $dns->getBarcodePNG(
            'TEST123',
            'C39',
            2,
            30,
            array(0, 0, 0),
            false,
            array(255, 255, 255)
        );

        $image = $this->createImage($png);
        $this->assertSame(-1, imagecolortransparent($image));
        $this->assertBackgroundColor($image, 255, 255, 255);
    }

    public function testDns1dAcceptsCustomBackgroundColor(): void
    {
        $dns = new DNS1D();
        $png = $dns->getBarcodePNG(
            'TEST123',
            'C39',
            2,
            30,
            array(0, 0, 0),
            false,
            array(200, 220, 240)
        );

        $image = $this->createImage($png);
        $this->assertSame(-1, imagecolortransparent($image));
        $this->assertBackgroundColor($image, 200, 220, 240);
    }

    public function testDns2dAcceptsCustomBackgroundOnPngPath(): void
    {
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'milon-barcode-qrbg-' . uniqid('', true);
        mkdir($dir);

        $dns = new DNS2D();
        $dns->setStorPath($dir);

        try {
            $path = $dns->getBarcodePNGPath(
                'https://example.com/path-bg',
                'QRCODE',
                3,
                3,
                array(0, 0, 0),
                array(255, 255, 255),
                'qr-bg'
            );
            $absolute = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR)
                . DIRECTORY_SEPARATOR
                . ltrim($path, '/\\');

            $this->assertFileExists($absolute);
            $image = imagecreatefrompng($absolute);
            $this->assertNotFalse($image);
            $this->assertSame(-1, imagecolortransparent($image));
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

    /**
     * @return resource|\GdImage
     */
    private function createImage(string $base64Png)
    {
        $image = imagecreatefromstring(base64_decode($base64Png));
        $this->assertNotFalse($image);

        return $image;
    }

    /**
     * @param resource|\GdImage $image
     */
    private function assertBackgroundColor($image, int $r, int $g, int $b): void
    {
        $colors = imagecolorsforindex($image, 0);

        $this->assertSame($r, $colors['red']);
        $this->assertSame($g, $colors['green']);
        $this->assertSame($b, $colors['blue']);
    }
}
