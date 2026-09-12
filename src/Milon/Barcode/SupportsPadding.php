<?php

namespace Milon\Barcode;

/**
 * Optional quiet-zone / margin around generated barcodes (print-scanner friendly).
 */
trait SupportsPadding
{
    /**
     * Padding in pixels applied on every side of the barcode.
     *
     * @var int
     */
    protected $padding = 0;

    /**
     * Set quiet-zone padding in pixels (all sides).
     *
     * For 1D print scanners, a common rule of thumb is about 10× the bar width
     * (e.g. `$w = 2` → `setPadding(20)`).
     *
     * @param int $padding
     * @return $this
     */
    public function setPadding($padding)
    {
        $this->padding = max(0, (int) $padding);

        return $this;
    }

    /**
     * @return int
     */
    public function getPadding()
    {
        return (int) $this->padding;
    }
}
