<?php
if (!function_exists('env')) {
    function env($key, $default = null) {
        $value = $_ENV[$key];
        return $value !== false ? $value : $default;
    }
}
