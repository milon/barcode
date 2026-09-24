---
title: Background color (dark UI / email)
---

# Background color (dark UI / email)

Transparent PNGs disappear on dark backgrounds. Pass a white (or light) `$bgcolor`:

| Transparent (default) | White background |
|-----------------------|------------------|
| ![Transparent QR](assets/examples/qrcode-transparent.png) | ![QR on white](assets/examples/qrcode-on-white.png) |

```php
// Recommended for dark themes and email clients
DNS2D::getBarcodePNG($url, 'QRCODE', 6, 6, [0, 0, 0], [255, 255, 255]);

DNS1D::getBarcodePNG('WHITEBG', 'C128', 3, 70, [0, 0, 0], false, [255, 255, 255]);
```

![CODE 128 with white background](assets/examples/c128-white-bg.png)

---
