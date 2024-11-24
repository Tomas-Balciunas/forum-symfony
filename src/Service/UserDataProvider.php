<?php

namespace App\Service;

use App\Entity\Permission;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

readonly class UserDataProvider
{
    public function __construct(private User $user, private ManagerRegistry $registry)
    {
    }

    public function getPermissions(): Collection
    {
        return $this->user->getPermissions();
    }

    public function getDefaultPermissions(): Collection
    {
        return $this->user->getDefaultPermissions();
    }

    public function getSpecialPermissions(): array
    {
        $repo = $this->registry->getRepository(Permission::class);
        return $repo->findNotOwnedPermissions($this->user->getRole()->getId());
    }

    public function userHasPermission(Permission $permission): bool
    {
        foreach ($this->getPermissions() as $userPermission) {
            if ($userPermission === $permission) {
                return true;
            }
        }

        return false;
    }

    public function formattedName(string $name): string
    {
        return ucfirst(join(' ', explode('.', $name)));
    }
}