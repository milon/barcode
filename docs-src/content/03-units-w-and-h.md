---
title: 'Units: `$w` and `$h`'
---

# Units: `$w` and `$h`

| API | `$w` | `$h` |
|-----|------|------|
| **DNS1D** (1D) | Width of a **single bar** in pixels | Total barcode height in pixels |
| **DNS2D** (QR / DM / PDF417) | Width of a **module** in pixels | Height of a **module** in pixels |

Larger `$w` / `$h` = larger image. Typical starting points:

- 1D labels: `$w = 2`–`3`, `$h = 50`–`80`
- QR for screen: `$w = $h = 4`–`8`
- QR for print: `$w = $h = 8`–`12`

---
