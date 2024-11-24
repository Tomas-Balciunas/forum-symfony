<?php

namespace App\Service;

use App\Entity\Permission;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserManager
{
    private User $user;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function grantPermission(Permission $permission): void
    {
        $this->user->setPermission($permission);
        $this->entityManager->flush();
    }

    public function revokePermission(Permission $permission): void
    {
        $this->user->getPermissions()->removeElement($permission);
        $this->entityManager->flush();
    }
}