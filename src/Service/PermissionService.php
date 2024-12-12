<?php

namespace App\Service;

use App\Entity\Permission;
use App\Entity\User;
use App\Exception\Permission\GrantPermissionException;
use App\Exception\Permission\RevokePermissionException;
use App\Validate\Permission\Grant\GrantPermissionValidator;
use App\Validate\Permission\Revoke\RevokePermissionValidator;
use Doctrine\ORM\EntityManagerInterface;

readonly class PermissionService
{
    public function __construct(
        private EntityManagerInterface    $manager,
        private GrantPermissionValidator  $grantPermissionValidator,
        private RevokePermissionValidator $revokePermissionValidator
    ) {}

    /**
     * @throws GrantPermissionException
     */
    public function handleGrantPermission(User $user, User $grantedBy, Permission $permission): void
    {
        $errors = $this->grantPermissionValidator->validate($user, $grantedBy, $permission)->getErrors();

        if (!empty($errors)) {
            throw new GrantPermissionException($errors);
        }

        $user->setPermission($permission);
        $this->manager->flush();
    }

    /**
     * @throws RevokePermissionException
     */
    public function handleRevokePermission(User $user, User $revokedBy, Permission $permission): void
    {
        $errors = $this->revokePermissionValidator->validate($user, $revokedBy, $permission)->getErrors();

        if (!empty($errors)) {
            throw new RevokePermissionException($errors);
        }

        $user->getPermissions()->removeElement($permission);
        $this->manager->flush();
    }
}