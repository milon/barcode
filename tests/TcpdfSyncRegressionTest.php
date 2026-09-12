<?php

namespace Milon\Barcode\Tests;

use Milon\Barcode\DNS2D;
use Milon\Barcode\Datamatrix;
use Milon\Barcode\PDF417;
use Milon\Barcode\QRcode;
use PHPUnit\Framework\TestCase;

/**
 * Regression coverage for TCPDF-synced 2D encoder fixes (#86, #88, #137, #163).
 */
class TcpdfSyncRegressionTest extends TestCase
{
    public function testDatamatrixEncodesIssue86Payload(): void
    {
        $matrix = new Datamatrix('764x 2.0-R20-TIALN');
        $arr = $matrix->getBarcodeArray();

        $this->assertIsArray($arr);
        $this->assertArrayHasKey('num_rows', $arr);
        $this->assertArrayHasKey('num_cols', $arr);
        $this->assertArrayHasKey('bcode', $arr);
        $this->assertGreaterThan(0, $arr['num_rows']);
        $this->assertGreaterThan(0, $arr['num_cols']);
    }

    public function testDatamatrixEncodesIssue163Payloads(): void
    {
        $payloads = array(
            'P31492604#T220609#0033#VAEOXK#',
            'P31492604#T220609#0033#VAEOXK#1',
            'P31492604-T220609-0033-VAEOXK2',
            'P31492604#T220609#ABCD#VAEOXK#',
        );

        foreach ($payloads as $payload) {
            $matrix = new Datamatrix($payload);
            $arr = $matrix->getBarcodeArray();

            $this->assertIsArray($arr, 'Failed for payload: ' . $payload);
            $this->assertArrayHasKey('bcode', $arr);
            $this->assertNotEmpty($arr['bcode']);
        }
    }

    public function testPdf417EncodesAnsiMh1083StylePayload(): void
    {
        // ANSI MH10.8.3-style sample (issue #137)
        $payload = '[)>+06' . chr(29) . '12VTEST' . chr(29) . 'P12345' . chr(30) . chr(4);
        $matrix = new PDF417($payload);
        $arr = $matrix->getBarcodeArray();

        $this->assertIsArray($arr);
        $this->assertArrayHasKey('num_rows', $arr);
        $this->assertArrayHasKey('num_cols', $arr);
        $this->assertNotEmpty($arr['bcode']);
    }

    public function testQrcodeGetCodeUsesIntegerIndexes(): void
    {
        $qr = new QRcode('https://example.com/tcpdf-sync', 'L');
        $arr = $qr->getBarcodeArray();

        $this->assertIsArray($arr);
        $this->assertArrayHasKey('bcode', $arr);
        $this->assertNotEmpty($arr['bcode']);
    }

    public function testDns2dSvgRendersSyncedEncoders(): void
    {
        $dns = new DNS2D();

        $datamatrix = $dns->getBarcodeSVG('764x 2.0-R20-TIALN', 'DATAMATRIX', 3, 3, 'black');
        $pdf417 = $dns->getBarcodeSVG('PDF417-SYNC-TEST', 'PDF417', 2, 1, 'black');
        $qr = $dns->getBarcodeSVG('qr-sync-test', 'QRCODE', 3, 3, 'black');

        $this->assertStringContainsString('<svg', $datamatrix);
        $this->assertStringContainsString('<svg', $pdf417);
        $this->assertStringContainsString('<svg', $qr);
    }
}
