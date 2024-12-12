<?php

namespace App\Validate\Permission\Grant;

use App\Entity\Permission;
use App\Entity\User;
use App\Validate\ValidatorInterface;

class GrantPermissionValidator extends GrantPermissionActions implements ValidatorInterface
{
    private array $errors = [];

    public function validate(User $user, User $grantedBy, Permission $permission): static
    {
        if ($this->isOwner($grantedBy, $user)) {
            $this->errors[] = 'Cannot grant permissions to yourself.';
        }

        if ($this->isAlreadyGranted($user, $permission)) {
            $this->errors[] = 'User already has this permission.';
        }

        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}