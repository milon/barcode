<?php

namespace Milon\Barcode\Tests;

use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use PHPUnit\Framework\TestCase;

class BarcodePathFilenameTest extends TestCase
{
    private $storageDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->storageDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'milon-barcode-path-' . uniqid('', true);
        mkdir($this->storageDir);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->storageDir)) {
            foreach (glob($this->storageDir . DIRECTORY_SEPARATOR . '*') ?: array() as $file) {
                unlink($file);
            }
            rmdir($this->storageDir);
        }
        parent::tearDown();
    }

    public function testDns1dStorePathKeepsDirectorySeparator(): void
    {
        $dns = new DNS1D();
        $dns->setStorPath($this->storageDir);

        $path = $dns->getBarcodePNGPath('14980758', 'C39', 2, 30);

        $this->assertStringEndsWith(DIRECTORY_SEPARATOR . '14980758.png', $this->absolutePath($path));
        $this->assertFileExists($this->absolutePath($path));
        unlink($this->absolutePath($path));
    }

    public function testDns2dCustomFilenameSeparateFromPayload(): void
    {
        $dns = new DNS2D();
        $dns->setStorPath($this->storageDir);

        $url = 'https://example.com/product/42';
        $path = $dns->getBarcodePNGPath($url, 'QRCODE', 3, 3, array(0, 0, 0), null, 'product-42');

        $absolute = $this->absolutePath($path);
        $this->assertStringEndsWith(DIRECTORY_SEPARATOR . 'product-42.png', $absolute);
        $this->assertFileExists($absolute);
        unlink($absolute);
    }

    public function testCustomFilenameStripsPathAndExtension(): void
    {
        $dns = new DNS1D();
        $dns->setStorPath($this->storageDir);

        $path = $dns->getBarcodePNGPath(
            'TEST',
            'C39',
            2,
            30,
            array(0, 0, 0),
            false,
            null,
            '../evil/my-barcode.png'
        );

        $absolute = $this->absolutePath($path);
        $this->assertSame($this->storageDir . DIRECTORY_SEPARATOR . 'my-barcode.png', $absolute);
        $this->assertFileExists($absolute);
        unlink($absolute);
    }

    private function absolutePath(string $relativePath): string
    {
        return rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($relativePath, '/\\');
    }
}
