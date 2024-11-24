<?php

namespace App\Service;

use App\Entity\Permission;
use App\Entity\User;

class AdminService
{
    public function __construct(private UserManager $userManager)
    {
    }

   public function handleGrantPermission(User $user, Permission $permission): void
   {
        $this->userManager->setUser($user);
        $this->userManager->grantPermission($permission);
   }

    public function handleRevokePermission(User $user, Permission $permission): void
    {
        $this->userManager->setUser($user);
        $this->userManager->revokePermission($permission);
   }

}