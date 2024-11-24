<?php

namespace App\Service;

use App\Entity\User;
use App\Service\Interface\PermissionManagerInterface;

class PermissionManager implements PermissionManagerInterface
{

    public function hasPermission(User $user, string $attribute): bool
    {
        $permissions = $user->getPermissions();
        $map = [];

        foreach ($permissions as $permission) {
            $map[] = $permission->getName();
        }

        return in_array($attribute, $map);
    }
}