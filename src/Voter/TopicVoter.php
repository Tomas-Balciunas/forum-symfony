<?php

namespace App\Voter;

use App\Data\Permissions;
use App\Entity\Topic;
use App\Entity\User;
use App\Service\Interface\PermissionManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class TopicVoter extends Voter implements VoterInterface
{
    public function __construct(private readonly PermissionManagerInterface $permissionManager)
    {
    }

    private const PERMISSIONS = [
        'create' => Permissions::TOPIC_CREATE,
        'delete' => Permissions::TOPIC_DELETE,
        'edit' => Permissions::TOPIC_EDIT,
        'lock' => Permissions::TOPIC_LOCK,
    ];

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, $this::PERMISSIONS, true)) {
            return false;
        }

        if ($attribute === self::PERMISSIONS['create']) {
            return true;
        }

        if (!$subject instanceof Topic) {
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
            self::PERMISSIONS['create'] => $this->canCreateTopic($user),
            self::PERMISSIONS['delete'] => $this->canDeleteTopic($user, $subject),
            self::PERMISSIONS['edit'] => $this->canEditTopic($user, $subject),
            self::PERMISSIONS['lock'] => $this->canLockTopic($user, $subject),
        };
    }

    private function canCreateTopic(User $user): bool
    {
        return !$this->permissionManager->isRestricted($user, self::PERMISSIONS['create']);
    }

    private function canDeleteTopic(User $user, Topic $topic): bool
    {
        if ($user->getRoles() === ['ROLE_ADMIN']) {
            return true;
        }

        if (!$this->isAuthor($user, $topic)) {
            return false;
        }

        return !$this->permissionManager->isRestricted($user, self::PERMISSIONS['delete']);
    }

    private function canEditTopic(User $user, Topic $topic): bool
    {
        if (!$this->isAuthor($user, $topic)) {
            return false;
        }

        return !$this->permissionManager->isRestricted($user, self::PERMISSIONS['edit']);
    }

    private function canLockTopic(User $user, Topic $topic): bool
    {
        if ($user->getRoles() === ['ROLE_ADMIN']) {
            return true;
        }

        if (!$this->isAuthor($user, $topic)) {
            return false;
        }

        return !$this->permissionManager->isRestricted($user, self::PERMISSIONS['lock']);
    }

    private function isAuthor(User $user, Topic $topic): bool
    {
        return $user === $topic->getAuthor();
    }
}
