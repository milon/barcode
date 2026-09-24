---
title: Standalone PHP (no Laravel)
---

# Standalone PHP (no Laravel)

Laravel is optional. Without a container, the store path defaults to the system temp directory (or whatever you set):

```php
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;

$d1 = new DNS1D();
$d1->setStorPath(__DIR__ . '/cache/');

echo $d1->getBarcodeHTML('9780691147727', 'EAN13');
echo '<img src="data:image/png;base64,' . $d1->getBarcodePNG('9780691147727', 'EAN13') . '">';

$d2 = new DNS2D();
$d2->setStorPath(__DIR__ . '/cache/');
file_put_contents(
    __DIR__ . '/cache/qr.png',
    base64_decode($d2->getBarcodePNG('https://example.com', 'QRCODE', 6, 6, [0, 0, 0], [255, 255, 255]))
);
```

Use the **instance** API (or the Laravel facade). Calling `DNS2D::getBarcodePNG()` as a static method on the class itself will not work.

---
