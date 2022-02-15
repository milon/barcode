<?php

namespace Milon\Barcode\Test;

use Milon\Barcode\BarcodeServiceProvider;
use Milon\Barcode\Facades\DNS1DFacade;
use Milon\Barcode\Facades\DNS2DFacade;

class ServiceProviderTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $serviceProvider = new BarcodeServiceProvider($this->app);
        $serviceProvider->register();
    }

    public function testFacadeDNS1D()
    {
        $svg = DNS1DFacade::getBarcodeSVG('4445645656', 'PHARMA2T');

        $this->assertNotEmpty($svg);
    }

    public function testFacadeDNS2D()
    {
        $svg = DNS2DFacade::getBarcodeSVG('4445645656', 'QRCODE');

        $this->assertNotEmpty($svg);
    }
}