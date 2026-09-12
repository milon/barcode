<?php

namespace Milon\Barcode;

if (!function_exists(__NAMESPACE__ . '\\public_path')) {
    function public_path()
    {
        return sys_get_temp_dir();
    }
}
