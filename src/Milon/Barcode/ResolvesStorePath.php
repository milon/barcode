<?php

namespace Milon\Barcode;

use Illuminate\Support\Str;

/**
 * Resolves barcode storage path without requiring a Laravel container.
 */
trait ResolvesStorePath
{
    /**
     * Ensure store_path is set, using Laravel config when available,
     * otherwise falling back to the system temp directory.
     *
     * @return void
     */
    protected function ensureStorePath()
    {
        if ($this->store_path) {
            return;
        }

        $this->setStorPath($this->resolveDefaultStorePath());
    }

    /**
     * @return string
     */
    protected function resolveDefaultStorePath()
    {
        if (function_exists('app')) {
            try {
                $config = app('config');
                if (is_object($config) && method_exists($config, 'get')) {
                    $path = $config->get('barcode.store_path');
                    if (!empty($path)) {
                        return $path;
                    }
                }
            } catch (\Throwable $e) {
                // Not running inside a Laravel app, or config is unbound.
            }
        }

        return sys_get_temp_dir();
    }

    /**
     * Normalize a storage directory path with a trailing separator.
     *
     * @param string $path
     * @return string
     */
    protected function normalizeStorePath($path)
    {
        return rtrim((string) $path, '/' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * Build a safe barcode filename (without extension).
     *
     * @param string $code barcode payload
     * @param string $suffix optional suffix mixed into the default slug (e.g. type)
     * @param string|null $filename optional custom filename (path/extension stripped)
     * @return string
     */
    protected function resolveBarcodeFilename($code, $suffix = '', $filename = null)
    {
        if ($filename !== null && $filename !== '') {
            $name = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, (string) $filename);
            $name = basename($name);
            $name = preg_replace('/\.(png|jpe?g)$/i', '', $name);
            if ($name !== '') {
                return $name;
            }
        }

        return Str::slug($code . $suffix);
    }
}
