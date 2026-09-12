<?php

namespace Milon\Barcode\Tests;

use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use PHPUnit\Framework\TestCase;

class StandaloneStorePathTest extends TestCase
{
    public function testDns1dWorksWithoutSetStorPath(): void
    {
        $dns = new DNS1D();
        $svg = $dns->getBarcodeSVG('TEST', 'C39', 2, 30, 'black', true, true);

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('TEST', $svg);
    }

    public function testDns2dWorksWithoutSetStorPath(): void
    {
        $dns = new DNS2D();
        $svg = $dns->getBarcodeSVG('https://example.com', 'QRCODE', 3, 3, 'black');

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('</svg>', $svg);
    }

    public function testDns1dHtmlWorksWithoutSetStorPath(): void
    {
        $dns = new DNS1D();
        $html = $dns->getBarcodeHTML('123456', 'C128');

        $this->assertStringContainsString('<div', $html);
    }
}
