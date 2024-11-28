<?php

namespace App\Service;

use App\Entity\Permission;
use App\Entity\User;
use App\Helper\PermissionHelper;
use Doctrine\Common\Collections\Collection;

readonly class UserDataProvider
{
    private User $user;

    public function __construct(private PermissionDataProvider $permissionProvider)
    {
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getPermissions(): Collection
    {
        return $this->user->getPermissions();
    }

    public function getDefaultPermissions(): Collection
    {
        return  $this->user->getDefaultPermissions();
    }

    public function getSpecialPermissions(): array
    {
        return $this->permissionProvider->findPermissionsNotOwnedBy($this->user);
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

    public function isSuspended(): bool
    {
        return $this->user->getSuspension() !== null;
    }
}