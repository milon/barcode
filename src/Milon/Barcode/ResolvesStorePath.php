<?php

namespace Milon\Barcode;

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
}
