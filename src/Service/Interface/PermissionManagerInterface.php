<?php

namespace App\Service\Interface;

use App\Entity\User;

interface PermissionManagerInterface
{
    public function hasPermission(User $user, string $attribute): bool;
}