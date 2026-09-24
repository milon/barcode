---
title: Symbology character sets
---

# Symbology character sets

Invalid input throws `Milon\Barcode\InvalidBarcodeException` with a hint for the chosen type.

| Type | Allowed data (practical) |
|------|--------------------------|
| **C39** | `0-9 A-Z` space and `-.$/+%` |
| **C39E** / **C39E+** | Full ASCII (extended CODE 39) |
| **C128** | Full ASCII; auto code-set |
| **C128A** | Uppercase / control; **no lowercase** — use C128B or C128 |
| **C128B** | Full ASCII |
| **C128C** | **Digits only**, encoded in pairs (even length) |
| **EAN13** | 12–13 digits (check digit calculated/validated) |
| **EAN8** | 7–8 digits |
| **UPCA** | 11–12 digits |
| **UPCE** | Compressed UPC-E digit form |
| **I25** / **S25** | Digits (I25 prefers even length) |
| **CODABAR** | Digits plus `-$:/.+` with A–D start/stop |
| **PHARMA** | Numeric pharmacode |
| **QRCODE** | Text/URL; capacity depends on version/ECC |
| **DATAMATRIX** | Text; capacity depends on symbol size |
| **PDF417** | Longer text payloads |

Prefer **C128** (auto) unless you specifically need A/B/C.

### Also supported (1D)

`C39+`, `C93`, `S25`, `S25+`, `I25+`, `GS1-128`, `EAN2`, `EAN5`, `MSI`, `MSI+`, `POSTNET`, `PLANET`, `RMS4CC`, `KIX`, `IMB`, `CODE11`, `PHARMA2T`

---
