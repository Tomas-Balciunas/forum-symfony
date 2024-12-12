<?php

namespace App\Service\Misc;

use App\Entity\Interface\AuthorInterface;
use App\Entity\User;

class OwnerChecker
{
    public function isOwner(User $user, mixed $entity): bool
    {
        if ($entity instanceof User) {
            return $user === $entity;
        }

        // AuthorInterface is implemented by all entities possessing a single user (currently Post and Topic)
        if ($entity instanceof AuthorInterface) {
            return $user === $entity->getAuthor();
        }

        return false;
    }
}