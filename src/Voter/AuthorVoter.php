<?php

declare(strict_types=1);

namespace App\Voter;

use App\Entity\Post;
use App\Entity\Topic;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AuthorVoter extends Voter
{
    const IS_AUTHOR = 'IS_AUTHOR';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute !== self::IS_AUTHOR) {
            return false;
        }

        if (!$subject instanceof Post && !$subject instanceof Topic) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return $subject->getAuthor() === $token->getUser();
    }
}
