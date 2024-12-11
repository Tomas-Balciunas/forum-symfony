<?php

namespace App\Validate\Permission\Revoke;

use App\Entity\Permission;
use App\Entity\User;
use App\Validate\ValidatorInterface;

class RevokePermissionValidator extends RevokePermissionActions implements ValidatorInterface
{
    private array $errors = [];

    public function validate(User $user, User $revokedBy, Permission $permission): static
    {
        if ($this->isOwner($revokedBy, $user)) {
            $this->errors[] = 'Cannot revoke your own permissions.';
        }

        if ($this->isAlreadyRevoked($user, $permission)) {
            $this->errors[] = 'This permission is already revoked.';
        }

        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}