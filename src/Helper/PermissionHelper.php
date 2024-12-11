<?php

namespace App\Helper;

class PermissionHelper
{
    public static function formatName(string $name): string
    {
        return ucfirst(join(' ', explode('.', $name)));
    }
}