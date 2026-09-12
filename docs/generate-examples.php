<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../tests/helpers.php';

use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;

$dir = __DIR__ . '/examples';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$d1 = new DNS1D();
$d2 = new DNS2D();
$d1->setStorPath($dir);
$d2->setStorPath($dir);

function saveScaled(string $path, $base64, int $scale = 2): void
{
    if ($base64 === false || $base64 === '') {
        throw new RuntimeException('Empty barcode for ' . $path);
    }
    $raw = base64_decode($base64, true);
    if ($raw === false) {
        throw new RuntimeException('Invalid base64 for ' . $path);
    }
    $src = imagecreatefromstring($raw);
    if ($src === false) {
        throw new RuntimeException('Invalid PNG for ' . $path . ' len=' . strlen($raw));
    }
    $w = imagesx($src);
    $h = imagesy($src);
    $dst = imagecreatetruecolor($w * $scale, $h * $scale);
    $white = imagecolorallocate($dst, 255, 255, 255);
    imagefill($dst, 0, 0, $white);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $w * $scale, $h * $scale, $w, $h);
    imagepng($dst, $path);
    echo basename($path) . " {$w}x{$h} -> " . ($w * $scale) . 'x' . ($h * $scale) . PHP_EOL;
}

$ones = array(
    'c39' => array('CODE39DEMO', 'C39', 3, 80, array(0, 0, 0)),
    'c39-plus' => array('CODE39', 'C39+', 3, 80, array(0, 0, 0)),
    'c128' => array('Code128Demo', 'C128', 3, 80, array(0, 0, 0)),
    'c128a' => array('CODE128A', 'C128A', 3, 80, array(0, 0, 0)),
    'c128b' => array('Code128B', 'C128B', 3, 80, array(0, 0, 0)),
    'c128c' => array('12345678', 'C128C', 3, 80, array(0, 0, 0)),
    'ean13' => array('5901234123457', 'EAN13', 3, 80, array(0, 0, 0)),
    'ean8' => array('96385074', 'EAN8', 3, 80, array(0, 0, 0)),
    'upca' => array('042100005264', 'UPCA', 3, 80, array(0, 0, 0)),
    'i25' => array('12345670', 'I25', 3, 80, array(0, 0, 0)),
    'codabar' => array('A123456A', 'CODABAR', 3, 80, array(0, 0, 0)),
    'pharma' => array('123456', 'PHARMA', 3, 80, array(0, 0, 0)),
    'c39-green' => array('GREENDEMO', 'C39', 3, 80, array(0, 128, 0)),
);

foreach ($ones as $name => $cfg) {
    list($code, $type, $w, $h, $color) = $cfg;
    $png = $d1->getBarcodePNG($code, $type, $w, $h, $color, true, array(255, 255, 255));
    saveScaled($dir . '/' . $name . '.png', $png, 2);
}

saveScaled($dir . '/c128-white-bg.png', $d1->getBarcodePNG('WHITEBG', 'C128', 3, 70, array(0, 0, 0), false, array(255, 255, 255)), 2);
saveScaled($dir . '/qrcode.png', $d2->getBarcodePNG('https://milon.im', 'QRCODE', 6, 6, array(0, 0, 0), array(255, 255, 255)), 2);
saveScaled($dir . '/qrcode-on-white.png', $d2->getBarcodePNG('https://milon.im', 'QRCODE', 6, 6, array(0, 0, 0), array(255, 255, 255)), 2);
saveScaled($dir . '/qrcode-transparent.png', $d2->getBarcodePNG('https://milon.im', 'QRCODE', 6, 6, array(0, 0, 0), null), 2);
saveScaled($dir . '/datamatrix.png', $d2->getBarcodePNG('DM-DEMO-12345', 'DATAMATRIX', 6, 6, array(0, 0, 0), array(255, 255, 255)), 2);
saveScaled($dir . '/pdf417.png', $d2->getBarcodePNG('PDF417 Demo Payload', 'PDF417', 3, 3, array(0, 0, 0), array(255, 255, 255)), 2);

$items = array(
    array('CODE 39', 'c39.png', "DNS1D::getBarcodePNG('CODE39DEMO', 'C39', 3, 80, [0,0,0], true, [255,255,255])"),
    array('CODE 39+', 'c39-plus.png', "DNS1D::getBarcodePNG('CODE39', 'C39+', 3, 80, [0,0,0], true, [255,255,255])"),
    array('CODE 128', 'c128.png', "DNS1D::getBarcodePNG('Code128Demo', 'C128', 3, 80, [0,0,0], true, [255,255,255])"),
    array('CODE 128A', 'c128a.png', "DNS1D::getBarcodePNG('CODE128A', 'C128A', 3, 80, [0,0,0], true, [255,255,255])"),
    array('CODE 128B', 'c128b.png', "DNS1D::getBarcodePNG('Code128B', 'C128B', 3, 80, [0,0,0], true, [255,255,255])"),
    array('CODE 128C', 'c128c.png', "DNS1D::getBarcodePNG('12345678', 'C128C', 3, 80, [0,0,0], true, [255,255,255])"),
    array('EAN-13', 'ean13.png', "DNS1D::getBarcodePNG('5901234123457', 'EAN13', 3, 80, [0,0,0], true, [255,255,255])"),
    array('EAN-8', 'ean8.png', "DNS1D::getBarcodePNG('96385074', 'EAN8', 3, 80, [0,0,0], true, [255,255,255])"),
    array('UPC-A', 'upca.png', "DNS1D::getBarcodePNG('042100005264', 'UPCA', 3, 80, [0,0,0], true, [255,255,255])"),
    array('Interleaved 2 of 5', 'i25.png', "DNS1D::getBarcodePNG('12345670', 'I25', 3, 80, [0,0,0], true, [255,255,255])"),
    array('Codabar', 'codabar.png', "DNS1D::getBarcodePNG('A123456A', 'CODABAR', 3, 80, [0,0,0], true, [255,255,255])"),
    array('Pharmacode', 'pharma.png', "DNS1D::getBarcodePNG('123456', 'PHARMA', 3, 80, [0,0,0], true, [255,255,255])"),
    array('Green CODE 39', 'c39-green.png', "DNS1D::getBarcodePNG('GREENDEMO', 'C39', 3, 80, [0,128,0], true, [255,255,255])"),
    array('CODE 128 white background', 'c128-white-bg.png', "DNS1D::getBarcodePNG('WHITEBG', 'C128', 3, 70, [0,0,0], false, [255,255,255])"),
    array('QR Code', 'qrcode.png', "DNS2D::getBarcodePNG('https://milon.im', 'QRCODE', 6, 6, [0,0,0], [255,255,255])"),
    array('Data Matrix', 'datamatrix.png', "DNS2D::getBarcodePNG('DM-DEMO-12345', 'DATAMATRIX', 6, 6, [0,0,0], [255,255,255])"),
    array('PDF417', 'pdf417.png', "DNS2D::getBarcodePNG('PDF417 Demo Payload', 'PDF417', 3, 3, [0,0,0], [255,255,255])"),
);

$html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>milon/barcode examples</title><style>
body{font-family:ui-sans-serif,system-ui,sans-serif;margin:0;background:#f6f7f9;color:#111}
header{padding:28px 32px;background:#111;color:#fff}
header h1{margin:0 0 6px;font-size:28px}
header p{margin:0;opacity:.8}
.grid{display:grid;grid-template-columns:minmax(0,1fr);gap:20px;padding:28px;max-width:920px;margin:0 auto}
.card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:18px}
.card h2{margin:0 0 12px;font-size:16px}
.card img{max-width:100%;height:auto;image-rendering:pixelated;background:#fff}
.card code{display:block;margin-top:12px;font-size:11px;background:#f3f4f6;padding:10px;border-radius:8px;overflow:auto;white-space:pre-wrap}
.dark{background:#1f2937;color:#fff}
.dark code{background:#111827;color:#e5e7eb}
.pair{display:flex;gap:16px;align-items:center}
.pair figure{margin:0;flex:1;text-align:center}
.pair figcaption{font-size:12px;margin-top:8px;opacity:.8}
</style></head><body><header><h1>milon/barcode</h1><p>Generated examples for documentation screenshots</p></header><section class="grid">';

foreach ($items as $item) {
    list($title, $img, $code) = $item;
    $id = preg_replace('/\.png$/', '', $img);
    $html .= '<article class="card" id="' . htmlspecialchars($id) . '"><h2>' . htmlspecialchars($title) . '</h2>';
    $html .= '<img src="' . htmlspecialchars($img) . '" alt="' . htmlspecialchars($title) . '">';
    $html .= '<code>' . htmlspecialchars($code) . '</code></article>';
}

$html .= '<article class="card dark" id="background"><h2>PNG background for dark UI / email</h2><div class="pair">';
$html .= '<figure><img src="qrcode-transparent.png" alt="transparent QR on dark"><figcaption>Transparent (hard to scan on dark)</figcaption></figure>';
$html .= '<figure><img src="qrcode-on-white.png" alt="white background QR"><figcaption>White background (recommended)</figcaption></figure>';
$html .= '</div><code>DNS2D::getBarcodePNG($url, \'QRCODE\', 6, 6, [0,0,0], [255,255,255])</code></article>';
$html .= '</section></body></html>';

file_put_contents($dir . '/gallery.html', $html);
echo "gallery written\n";

$shotDir = $dir . '/screenshots';
if (!is_dir($shotDir)) {
    mkdir($shotDir, 0777, true);
}

// Composite each example onto a light card for README / issue screenshots.
foreach ($items as $item) {
    list($title, $img) = $item;
    $srcPath = $dir . '/' . $img;
    $src = imagecreatefrompng($srcPath);
    if ($src === false) {
        throw new RuntimeException('Cannot read ' . $srcPath);
    }
    $iw = imagesx($src);
    $ih = imagesy($src);
    $pad = 24;
    $header = 36;
    $cardW = max(640, $iw + ($pad * 2));
    $cardH = $ih + $pad * 2 + $header;
    $card = imagecreatetruecolor($cardW, $cardH);
    $white = imagecolorallocate($card, 255, 255, 255);
    $ink = imagecolorallocate($card, 17, 17, 17);
    $border = imagecolorallocate($card, 229, 231, 235);
    imagefilledrectangle($card, 0, 0, $cardW - 1, $cardH - 1, $white);
    imagerectangle($card, 0, 0, $cardW - 1, $cardH - 1, $border);
    imagestring($card, 5, $pad, 12, $title, $ink);
    imagecopy($card, $src, (int) (($cardW - $iw) / 2), $pad + $header, 0, 0, $iw, $ih);
    $out = $shotDir . '/' . preg_replace('/\.png$/', '', $img) . '.png';
    imagepng($card, $out);
    echo 'screenshot ' . basename($out) . PHP_EOL;
}

// Dark-theme comparison card
$left = imagecreatefrompng($dir . '/qrcode-transparent.png');
$right = imagecreatefrompng($dir . '/qrcode-on-white.png');
$lw = imagesx($left);
$lh = imagesy($left);
$rw = imagesx($right);
$rh = imagesy($right);
$pad = 24;
$cardW = $pad * 3 + $lw + $rw;
$cardH = max($lh, $rh) + $pad * 2 + 48;
$card = imagecreatetruecolor($cardW, $cardH);
$bg = imagecolorallocate($card, 31, 41, 55);
$fg = imagecolorallocate($card, 255, 255, 255);
imagefilledrectangle($card, 0, 0, $cardW - 1, $cardH - 1, $bg);
imagestring($card, 5, $pad, 14, 'Transparent vs white background', $fg);
imagecopy($card, $left, $pad, 48, 0, 0, $lw, $lh);
imagecopy($card, $right, $pad * 2 + $lw, 48, 0, 0, $rw, $rh);
imagepng($card, $shotDir . '/background.png');
echo "screenshot background.png\n";
