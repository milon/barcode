---
title: Installation
---

# Installation

```shell
composer require milon/barcode
```

### Laravel compatibility

| Laravel | Package |
|---------|---------|
| 13.* | ^13.0 |
| 12.* | ^12.0 |
| 11.* | ^11.0 |
| 10.* | ^10.0 |
| 9.* | ^9.0 |
| 8.* | ^8.0 |
| 7.* | ^7.0 |
| 6.* | ^6.0 |
| 5.0–5.1 | ^5.1 |
| 4.x | ^4.2 |

Laravel 6+ auto-discovers the service provider and facades (`DNS1D`, `DNS2D`).

### Publish config (optional)

```shell
php artisan vendor:publish --provider="Milon\Barcode\BarcodeServiceProvider"
```

Default store path is the Laravel `storage` directory (used by `*Path` helpers). Ensure the directory is writable.

---
