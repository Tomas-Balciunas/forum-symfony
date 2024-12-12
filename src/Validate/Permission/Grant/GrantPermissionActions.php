<?php

namespace App\Validate\Permission\Grant;

use App\Entity\Permission;
use App\Entity\User;
use App\Service\Misc\OwnerChecker;
use App\Service\Misc\UserFullDataProvider;

class GrantPermissionActions
{
    public function __construct(
        private readonly UserFullDataProvider $userDataProvider,
        private readonly OwnerChecker $ownerChecker
    ) {}

    protected function isOwner(User $grantedBy, User $user): bool
    {
        return $this->ownerChecker->isOwner($grantedBy, $user);
    }

    protected function isAlreadyGranted(User $user, Permission $permission): bool
    {
        $this->userDataProvider->setUser($user);

        return $this->userDataProvider->hasPermission($permission);
    }
}