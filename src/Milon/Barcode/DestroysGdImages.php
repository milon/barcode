<?php

namespace Milon\Barcode;

/**
 * Safely destroy GD image handles across PHP versions.
 *
 * On PHP 8+, GD returns GdImage objects and imagedestroy() is a no-op that
 * is deprecated in PHP 8.5. On PHP 7, GD returns resources that still need
 * imagedestroy().
 *
 * Behavior matches PR #209; this trait just centralizes the check.
 */
trait DestroysGdImages
{
    /**
     * @param resource|\GdImage|mixed $image
     * @return void
     */
    protected function destroyGdImage($image)
    {
        if (is_resource($image)) {
            imagedestroy($image);
        }
    }
}
