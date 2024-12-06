<?php

namespace App\Service;

use App\Entity\Permission;
use Doctrine\Common\Collections\Collection;

class UserFullDataProvider extends UserDataProvider
{

    public function getDefaultPermissions(): Collection
    {
        return $this->user->getDefaultPermissions();
    }

    public function hasPermission(Permission $permission): bool
    {
        foreach ($this->getPermissions() as $userPermission) {
            if ($userPermission === $permission) {
                return true;
            }
        }

        return false;
    }

    public function getPermissions(): Collection
    {
        return $this->user->getPermissions();
    }

    public function getSpecialPermissions(): array
    {
        return $this->permissionProvider->findPermissionsNotOwnedBy($this->user);
    }
}