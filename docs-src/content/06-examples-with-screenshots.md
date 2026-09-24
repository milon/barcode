---
title: Examples (with screenshots)
---

# Examples (with screenshots)

### CODE 39

![CODE 39](assets/examples/c39.png)

```php
DNS1D::getBarcodePNG('CODE39DEMO', 'C39', 3, 80, [0, 0, 0], true, [255, 255, 255]);
```

### CODE 39+

![CODE 39+](assets/examples/c39-plus.png)

```php
DNS1D::getBarcodePNG('CODE39', 'C39+', 3, 80, [0, 0, 0], true, [255, 255, 255]);
```

### CODE 128

![CODE 128](assets/examples/c128.png)

```php
DNS1D::getBarcodePNG('Code128Demo', 'C128', 3, 80, [0, 0, 0], true, [255, 255, 255]);
```

### CODE 128A / 128B / 128C

| C128A | C128B | C128C |
|-------|-------|-------|
| ![C128A](assets/examples/c128a.png) | ![C128B](assets/examples/c128b.png) | ![C128C](assets/examples/c128c.png) |

```php
DNS1D::getBarcodePNG('CODE128A', 'C128A', 3, 80, [0, 0, 0], true, [255, 255, 255]);
DNS1D::getBarcodePNG('Code128B', 'C128B', 3, 80, [0, 0, 0], true, [255, 255, 255]);
DNS1D::getBarcodePNG('12345678', 'C128C', 3, 80, [0, 0, 0], true, [255, 255, 255]); // digits, even length
```

### EAN-13 / EAN-8 / UPC-A

| EAN-13 | EAN-8 | UPC-A |
|--------|-------|-------|
| ![EAN-13](assets/examples/ean13.png) | ![EAN-8](assets/examples/ean8.png) | ![UPC-A](assets/examples/upca.png) |

```php
DNS1D::getBarcodePNG('5901234123457', 'EAN13', 3, 80, [0, 0, 0], true, [255, 255, 255]);
DNS1D::getBarcodePNG('96385074', 'EAN8', 3, 80, [0, 0, 0], true, [255, 255, 255]);
DNS1D::getBarcodePNG('042100005264', 'UPCA', 3, 80, [0, 0, 0], true, [255, 255, 255]);
```

### Interleaved 2 of 5 / Codabar / Pharmacode

| I25 | Codabar | Pharmacode |
|-----|---------|------------|
| ![I25](assets/examples/i25.png) | ![Codabar](assets/examples/codabar.png) | ![Pharmacode](assets/examples/pharma.png) |

```php
DNS1D::getBarcodePNG('12345670', 'I25', 3, 80, [0, 0, 0], true, [255, 255, 255]);
DNS1D::getBarcodePNG('A123456A', 'CODABAR', 3, 80, [0, 0, 0], true, [255, 255, 255]);
DNS1D::getBarcodePNG('123456', 'PHARMA', 3, 80, [0, 0, 0], true, [255, 255, 255]);
```

### Custom bar color

![Green CODE 39](assets/examples/c39-green.png)

```php
DNS1D::getBarcodePNG('GREENDEMO', 'C39', 3, 80, [0, 128, 0], true, [255, 255, 255]);
```

### QR Code / Data Matrix / PDF417

| QR Code | Data Matrix | PDF417 |
|---------|-------------|--------|
| ![QR](assets/examples/qrcode.png) | ![Data Matrix](assets/examples/datamatrix.png) | ![PDF417](assets/examples/pdf417.png) |

```php
DNS2D::getBarcodePNG('https://milon.im', 'QRCODE', 6, 6, [0, 0, 0], [255, 255, 255]);
DNS2D::getBarcodePNG('DM-DEMO-12345', 'DATAMATRIX', 6, 6, [0, 0, 0], [255, 255, 255]);
DNS2D::getBarcodePNG('PDF417 Demo Payload', 'PDF417', 3, 3, [0, 0, 0], [255, 255, 255]);
```

Browse all generated samples: [example gallery](assets/examples/gallery.html).

Regenerate images + gallery:

```shell
php docs/generate-examples.php
```

---
