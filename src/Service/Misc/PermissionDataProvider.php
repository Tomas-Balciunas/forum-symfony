<?php

namespace App\Service\Misc;

use App\Entity\Permission;
use App\Entity\User;
use App\Repository\PermissionRepository;
use App\Service\Interface\PermissionManagerInterface;

readonly class PermissionDataProvider implements PermissionManagerInterface
{
    public function __construct(private PermissionRepository $repository)
    {
    }

    public function findPermissionsNotOwnedBy(User $user): array
    {
        return $this->repository->findNotOwnedPermissions($user->getRole()->getId());
    }

    public function getPermissionByName(string $name): ?Permission
    {
        return $this->repository->findPermissionByName($name);
    }
}