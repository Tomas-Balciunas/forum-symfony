<?php

namespace App\Voter;

use App\Data\Permissions;
use App\Data\Roles;
use App\Entity\Permission;
use App\Entity\Topic;
use App\Entity\User;
use App\Service\Misc\OwnerChecker;
use App\Service\PermissionDataProvider;
use App\Service\UserFullDataProvider;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class TopicVoter extends Voter implements VoterInterface
{
    private const PERMISSIONS = [
        'delete' => Permissions::TOPIC_DELETE,
        'edit' => Permissions::TOPIC_EDIT,
        'lock' => Permissions::TOPIC_LOCK,
        'show' => Permissions::TOPIC_SET_VISIBLE,
        'hide' => Permissions::TOPIC_SET_HIDDEN,
        'move' => Permissions::TOPIC_MOVE,
    ];

    private Permission $permission;

    public function __construct(
        private readonly UserFullDataProvider   $userProvider,
        private readonly PermissionDataProvider $permissionProvider,
        private readonly OwnerChecker           $ownerChecker
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, $this::PERMISSIONS, true)) {
            return false;
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

        $this->permission = $this->permissionProvider->getPermissionByName($attribute);
        $this->userProvider->setUser($user);

        return match ($attribute) {
            self::PERMISSIONS['delete'] => $this->canDeleteTopic($user, $subject),
            self::PERMISSIONS['edit'] => $this->canEditTopic($user, $subject),
            self::PERMISSIONS['lock'] => $this->canLockTopic($user, $subject),
            self::PERMISSIONS['show'] => $this->canSetTopicVisible($user, $subject),
            self::PERMISSIONS['hide'] => $this->canHideTopic($user, $subject),
            default => $this->userProvider->hasPermission($this->permission)
        };
    }

    private function canDeleteTopic(User $user, Topic $topic): bool
    {
        if ($user->getRole()->getName() === Roles::ROLE_ADMIN) {
            return true;
        }

        if (!$this->ownerChecker->isOwner($user, $topic)) {
            return false;
        }

        return $this->userProvider->hasPermission($this->permission);
    }

    private function canEditTopic(User $user, Topic $topic): bool
    {
        if (!$this->ownerChecker->isOwner($user, $topic)) {
            return false;
        }

        return $this->userProvider->hasPermission($this->permission);
    }

    private function canLockTopic(User $user, Topic $topic): bool
    {
        if ($user->getRole()->getName() === Roles::ROLE_ADMIN) {
            return true;
        }

        if (!$this->ownerChecker->isOwner($user, $topic)) {
            return false;
        }

        return $this->userProvider->hasPermission($this->permission);
    }

    private function canSetTopicVisible(User $user, Topic $topic): bool
    {
        if (!$this->ownerChecker->isOwner($user, $topic)) {
            return false;
        }

        return $this->userProvider->hasPermission($this->permission);
    }

    private function canHideTopic(User $user, Topic $topic): bool
    {
        if ($user->getRole()->getName() === Roles::ROLE_ADMIN) {
            return true;
        }

        if (!$this->ownerChecker->isOwner($user, $topic)) {
            return false;
        }

        return $this->userProvider->hasPermission($this->permission);
    }
}
