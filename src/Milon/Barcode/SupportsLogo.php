<?php

namespace Milon\Barcode;

/**
 * Optional centered logo overlay for 2D barcodes (QR / Datamatrix PNGs).
 */
trait SupportsLogo
{
    /**
     * Absolute path to a PNG/JPEG/GIF logo, or null.
     *
     * @var string|null
     */
    protected $logoPath = null;

    /**
     * Logo width as a fraction of the barcode width (0.05–0.3 recommended).
     *
     * @var float
     */
    protected $logoWidthPercent = 0.2;

    /**
     * Set a centered logo image for subsequent PNG renders.
     *
     * Prefer high error correction for QR (e.g. type `QRCODE,H`) so the
     * covered modules remain scannable.
     *
     * @param string|null $path filesystem path to an image, or null to clear
     * @param float $widthPercent fraction of barcode width (clamped to 0.05–0.35)
     * @return $this
     */
    public function setLogo($path, $widthPercent = 0.2)
    {
        if ($path === null || $path === '') {
            $this->logoPath = null;
        } else {
            $this->logoPath = (string) $path;
        }
        $this->logoWidthPercent = max(0.05, min(0.35, (float) $widthPercent));

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLogoPath()
    {
        return $this->logoPath;
    }

    /**
     * Composite the configured logo onto a GD image (center + white pad).
     *
     * @param resource|\GdImage $png
     * @param int $width
     * @param int $height
     * @return void
     */
    protected function applyLogoToGdImage($png, $width, $height)
    {
        if ($this->logoPath === null || $this->logoPath === '' || !is_readable($this->logoPath)) {
            return;
        }

        $logoData = @file_get_contents($this->logoPath);
        if ($logoData === false) {
            return;
        }
        $logo = @imagecreatefromstring($logoData);
        if ($logo === false) {
            return;
        }

        $targetW = (int) max(1, round($width * $this->logoWidthPercent));
        $srcW = imagesx($logo);
        $srcH = imagesy($logo);
        if ($srcW < 1 || $srcH < 1) {
            return;
        }
        $targetH = (int) max(1, round($targetW * ($srcH / $srcW)));

        $pad = (int) max(2, round($targetW * 0.12));
        $boxW = $targetW + (2 * $pad);
        $boxH = $targetH + (2 * $pad);
        $dstX = (int) (($width - $boxW) / 2);
        $dstY = (int) (($height - $boxH) / 2);

        $white = imagecolorallocate($png, 255, 255, 255);
        imagefilledrectangle($png, $dstX, $dstY, $dstX + $boxW - 1, $dstY + $boxH - 1, $white);

        $scaled = imagecreatetruecolor($targetW, $targetH);
        imagealphablending($scaled, false);
        imagesavealpha($scaled, true);
        $transparent = imagecolorallocatealpha($scaled, 0, 0, 0, 127);
        imagefilledrectangle($scaled, 0, 0, $targetW, $targetH, $transparent);
        imagecopyresampled($scaled, $logo, 0, 0, 0, 0, $targetW, $targetH, $srcW, $srcH);

        imagealphablending($png, true);
        imagecopy($png, $scaled, $dstX + $pad, $dstY + $pad, 0, 0, $targetW, $targetH);
    }
}
