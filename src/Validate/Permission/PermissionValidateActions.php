<?php

namespace App\Validate\Permission;

use App\Entity\Permission;
use App\Entity\User;
use App\Exception\Permission\PermissionAlreadyGrantedException;
use App\Service\UserDataProvider;

class PermissionValidateActions
{
    public function __construct(private readonly UserDataProvider $userDataProvider)
    {
    }

    protected function isAlreadyGranted(User $user, Permission $permission): void
    {
        $this->userDataProvider->setUser($user);

        if($this->userDataProvider->hasPermission($permission)) {
            throw new PermissionAlreadyGrantedException($permission->getName());
        }
    }
}