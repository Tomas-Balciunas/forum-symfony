<?php

namespace App\Helper;

use Doctrine\Common\Collections\Collection;

class PermissionHelper
{
    public static function formatName(string $name): string
    {
        return ucfirst(join(' ', explode('.', $name)));
    }
}