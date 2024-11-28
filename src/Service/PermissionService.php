<?php

namespace App\Service;

use App\Entity\Permission;
use App\Entity\User;
use App\Validate\Permission\PermissionValidator;
use Doctrine\ORM\EntityManagerInterface;

readonly class PermissionService
{
    public function __construct(private EntityManagerInterface $manager, private PermissionValidator $validator)
    {
    }

    public function handleGrantPermission(User $user, Permission $permission): void
    {
        $this->validator->validateGrantPermission($user, $permission);

        $user->setPermission($permission);
        $this->manager->flush();
    }

    public function handleRevokePermission(User $user, Permission $permission): void
    {

        $user->getPermissions()->removeElement($permission);
        $this->manager->flush();
    }
}