<?php

use App\Services\Hashid;

if (!function_exists('hid')) {
    /** Encode a numeric ID to a URL-safe hash string. */
    function hid(string $type, int $id): string
    {
        return Hashid::encode($type, $id);
    }
}
