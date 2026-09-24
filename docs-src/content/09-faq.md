---
title: FAQ
---

# FAQ

**Why is my Blade barcode empty / escaped HTML?**  
Use `{!! DNS1D::getBarcodeHTML(...) !!}`, not `{{ }}`.

**Why is the `<img>` broken?**  
PNG helpers return base64 only. Prefix with `data:image/png;base64,`.

**Barcode works in light mode but not dark mode / email?**  
Pass a solid `$bgcolor`, e.g. `[255, 255, 255]`.

**Printed barcode won’t scan reliably?**  
Add a quiet zone (`setPadding(...)`), use a larger `$w` (avoid `$w = 1` for print), and prefer SVG/PNG over scaled-down screen captures. See [Quiet zone](04-output-methods.html#quiet-zone-padding-for-print-scanners).

**`InvalidBarcodeException`?**  
The payload does not match the symbology (see table above). Switch type or sanitize input.

**Can this package scan barcodes from a camera?**  
No. It only **generates** barcodes. Scanning needs a separate library or device SDK.

**Can it authenticate users / replace login?**  
No. Generating a QR of a URL or token is not authentication by itself.

---
