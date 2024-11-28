<?php

namespace App\Validate\Permission;

use App\Entity\Permission;
use App\Entity\User;

class PermissionValidator extends PermissionValidateActions
{
    public function validateGrantPermission(User $user, Permission $permission): void
    {
        $this->isAlreadyGranted($user, $permission);
    }
}