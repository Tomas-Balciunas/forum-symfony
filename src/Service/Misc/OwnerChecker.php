<?php

namespace App\Service\Misc;

use App\Entity\Interface\AuthorInterface;
use App\Entity\User;
use Psr\Log\LoggerInterface;

class OwnerChecker
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function isOwner(User $user, mixed $entity): bool
    {
        if ($entity instanceof User) {
            if ($user === $entity) {
                return true;
            }

            $this->logger->warning('Unauthorized access attempt: User {userId} tried to access a resource owned by {ownerId}.', [
                'userId' => $user->getId() ?? 'unknown',
                'owner' => $entity->getId() ?? 'unknown',
            ]);

            return false;
        }

        // AuthorInterface is implemented by all entities possessing a single user (currently Post and Topic)
        if ($entity instanceof AuthorInterface) {
            if ($user === $entity->getAuthor()) {
                return true;
            }

            $this->logger->warning('Unauthorized access attempt: non-author {userId} tried to access a resource {class} id: {entityId} owned by user {owner}.', [
                'userId' => $user->getId() ?? 'unknown',
                'class' => $entity::class,
                'entityId' => $entity->getId() ?? 'unknown',
                'owner' => $entity->getAuthor()?->getId() ?? 'unknown',
            ]);

            return false;
        }

        return false;
    }
}