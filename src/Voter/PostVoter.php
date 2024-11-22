<?php

namespace App\Voter;

use App\Data\Permissions;
use App\Entity\Post;
use App\Entity\User;
use App\Service\Interface\PermissionManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class PostVoter extends Voter implements VoterInterface
{
    public function __construct(private readonly PermissionManagerInterface $permissionManager)
    {
    }

    private const PERMISSIONS = [
        'create' => Permissions::POST_CREATE,
        'delete' => Permissions::POST_DELETE,
        'edit' => Permissions::POST_EDIT,
    ];

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, self::PERMISSIONS, true)) {
            return false;
        }

        if ($attribute === self::PERMISSIONS['create']) {
            return true;
        }

        if (!$subject instanceof Post) {
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

        return match ($attribute) {
            self::PERMISSIONS['create'] => $this->canCreatePost($user),
            self::PERMISSIONS['delete'] => $this->canDeletePost($user, $subject),
            self::PERMISSIONS['edit'] => $this->canEditPost($user, $subject),
        };
    }

    private function canCreatePost(User $user): bool
    {
        return !$this->permissionManager->isRestricted($user, self::PERMISSIONS['create']);
    }

    private function canDeletePost(User $user, Post $post): bool
    {
        if ($user->getRoles() === ['ROLE_ADMIN']) {
            return true;
        }

        if (!$this->isAuthor($user, $post)) {
            return false;
        }

        return !$this->permissionManager->isRestricted($user, self::PERMISSIONS['delete']);
    }

    private function canEditPost(User $user, Post $post): bool
    {
        if (!$this->isAuthor($user, $post)) {
            return false;
        }

        return !$this->permissionManager->isRestricted($user, self::PERMISSIONS['edit']);
    }

    private function isAuthor(User $user, Post $post): bool
    {
        return $user === $post->getAuthor();
    }
}
