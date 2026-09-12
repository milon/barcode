<?php

namespace Milon\Barcode;

function public_path()
{
    return sys_get_temp_dir();
}

namespace Milon\Barcode\Tests;

use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use PHPUnit\Framework\TestCase;

class DeprecationCompatibilityTest extends TestCase
{
    public function testDns1dBarcodeGenerationDoesNotTriggerDeprecations(): void
    {
        $dns1d = new DNS1D();
        $dns1d->setStorPath($this->temporaryStoragePath());

        list($pngBarcode, $pngDeprecations) = $this->captureDeprecations(function () use ($dns1d) {
            return $dns1d->getBarcodePNG('123456789012', 'C128');
        });

        $this->assertNotFalse($pngBarcode);
        $this->assertSame(array(), $pngDeprecations);

        list($jpgBarcode, $jpgDeprecations) = $this->captureDeprecations(function () use ($dns1d) {
            return $dns1d->getBarcodeJPG('123456789012', 'C128');
        });

        $this->assertNotFalse($jpgBarcode);
        $this->assertSame(array(), $jpgDeprecations);

        list($pngPath, $pngPathDeprecations) = $this->captureDeprecations(function () use ($dns1d) {
            return $dns1d->getBarcodePNGPath('123456789012', 'C128');
        });

        $this->assertStringEndsWith('.png', $pngPath);
        $this->assertFileExists($this->toAbsoluteTempPath($pngPath));
        unlink($this->toAbsoluteTempPath($pngPath));
        $this->assertSame(array(), $pngPathDeprecations);

        list($jpgPath, $jpgPathDeprecations) = $this->captureDeprecations(function () use ($dns1d) {
            return $dns1d->getBarcodeJPGPath('123456789012', 'C128');
        });

        $this->assertStringEndsWith('.jpg', $jpgPath);
        $this->assertFileExists($this->toAbsoluteTempPath($jpgPath));
        unlink($this->toAbsoluteTempPath($jpgPath));
        $this->assertSame(array(), $jpgPathDeprecations);
    }

    public function testDns2dBarcodeGenerationDoesNotTriggerDeprecations(): void
    {
        $dns2d = new DNS2D();
        $dns2d->setStorPath($this->temporaryStoragePath());

        list($pngBarcode, $pngDeprecations) = $this->captureDeprecations(function () use ($dns2d) {
            return $dns2d->getBarcodePNG('https://example.com', 'QRCODE');
        });

        $this->assertNotFalse($pngBarcode);
        $this->assertSame(array(), $pngDeprecations);

        list($pngPath, $pngPathDeprecations) = $this->captureDeprecations(function () use ($dns2d) {
            return $dns2d->getBarcodePNGPath('https://example.com', 'QRCODE');
        });

        $this->assertStringEndsWith('.png', $pngPath);
        $this->assertFileExists($this->toAbsoluteTempPath($pngPath));
        unlink($this->toAbsoluteTempPath($pngPath));
        $this->assertSame(array(), $pngPathDeprecations);
    }

    private function captureDeprecations(callable $callback): array
    {
        $deprecations = array();

        set_error_handler(function ($severity, $message) use (&$deprecations) {
            if ($severity === E_DEPRECATED || $severity === E_USER_DEPRECATED) {
                $deprecations[] = $message;

                return true;
            }

            return false;
        });

        try {
            $result = $callback();
        } finally {
            restore_error_handler();
        }

        return array($result, $deprecations);
    }

    private function toAbsoluteTempPath(string $relativePath): string
    {
        return rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($relativePath, '/\\');
    }

    private function temporaryStoragePath(): string
    {
        return rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }
}
