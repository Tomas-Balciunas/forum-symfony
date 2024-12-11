<?php

namespace App\Validate\Permission\Revoke;

use App\Entity\Permission;
use App\Entity\User;
use App\Service\Misc\OwnerChecker;
use App\Service\Misc\UserFullDataProvider;

class RevokePermissionActions
{
    public function __construct(
        private readonly UserFullDataProvider $userDataProvider,
        private readonly OwnerChecker $ownerChecker
    ) {}

    protected function isOwner(User $revokedBy, User $user): bool
    {
        return $this->ownerChecker->isOwner($revokedBy, $user);
    }

    protected function isAlreadyRevoked(User $user, Permission $permission): bool
    {
        $this->userDataProvider->setUser($user);

        return !$this->userDataProvider->hasPermission($permission);
    }
}