<?php

namespace App\Service;

class UserFullDataProvider extends UserDataProvider
{
    public function getSpecialPermissions(): array
    {
        return $this->permissionProvider->findPermissionsNotOwnedBy($this->user);
    }
}