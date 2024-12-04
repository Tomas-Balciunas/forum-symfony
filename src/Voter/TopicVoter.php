<?php

namespace App\Voter;

use App\Data\Permissions;
use App\Data\Roles;
use App\Entity\Permission;
use App\Entity\Topic;
use App\Entity\User;
use App\Service\PermissionDataProvider;
use App\Service\UserDataProvider;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class TopicVoter extends Voter implements VoterInterface
{
    private const PERMISSIONS = [
        'delete' => Permissions::TOPIC_DELETE,
        'edit' => Permissions::TOPIC_EDIT,
        'lock' => Permissions::TOPIC_LOCK,
        'createPost' => Permissions::POST_CREATE,
    ];

    private Permission $permission;

    public function __construct(private readonly UserDataProvider       $userProvider,
                                private readonly PermissionDataProvider $permissionProvider)
    {
    }

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

        $this->permission = $this->permissionProvider->getPermissionByName($attribute);
        $this->userProvider->setUser($user);

        return match ($attribute) {
            self::PERMISSIONS['delete'] => $this->canDeleteTopic($user, $subject),
            self::PERMISSIONS['edit'] => $this->canEditTopic($user, $subject),
            self::PERMISSIONS['lock'] => $this->canLockTopic($user, $subject),
            self::PERMISSIONS['createPost'] => $this->canCreatePost($subject)
        };
    }

    private function canCreatePost(Topic $topic): bool
    {
        if ($topic->isLocked()) {
            return false;
        }

        return $this->userProvider->hasPermission($this->permission);
    }

    private function canDeleteTopic(User $user, Topic $topic): bool
    {
        if ($user->getRole()->getName() === Roles::ROLE_ADMIN) {
            return true;
        }

        if (!$this->isAuthor($user, $topic)) {
            return false;
        }

        return $this->userProvider->hasPermission($this->permission);
    }

    private function isAuthor(User $user, Topic $topic): bool
    {
        return $user === $topic->getAuthor();
    }

    private function canEditTopic(User $user, Topic $topic): bool
    {
        if (!$this->isAuthor($user, $topic)) {
            return false;
        }

        return $this->userProvider->hasPermission($this->permission);
    }

    private function canLockTopic(User $user, Topic $topic): bool
    {
        if ($user->getRole()->getName() === Roles::ROLE_ADMIN) {
            return true;
        }

        if (!$this->isAuthor($user, $topic)) {
            return false;
        }

        return $this->userProvider->hasPermission($this->permission);
    }
}
