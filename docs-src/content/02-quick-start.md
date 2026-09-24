---
title: Quick start
---

# Quick start

```php
use DNS1D;
use DNS2D;

// SVG / HTML (ready to echo)
echo DNS1D::getBarcodeSVG('CODE39DEMO', 'C39');
echo DNS2D::getBarcodeHTML('https://example.com', 'QRCODE');

// PNG / JPEG as base64 (use the data URI form)
echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG('CODE39DEMO', 'C39') . '" alt="barcode">';
echo '<img src="data:image/png;base64,' . DNS2D::getBarcodePNG('https://example.com', 'QRCODE') . '" alt="qr">';
```

### Blade

Always use `{!! !!}` (unescaped). PNG helpers return **raw base64**, not a full `data:` URL:

```blade
{!! DNS1D::getBarcodeHTML('5901234123457', 'EAN13') !!}

<img
  src="data:image/png;base64,{{ DNS2D::getBarcodePNG($url, 'QRCODE', 6, 6, [0,0,0], [255,255,255]) }}"
  alt="QR code"
>
```

---
