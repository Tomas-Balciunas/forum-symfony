<?php

namespace App\Voter;

use App\Data\Permissions;
use App\Data\Roles;
use App\Entity\Permission;
use App\Entity\Post;
use App\Entity\User;
use App\Service\Misc\OwnerChecker;
use App\Service\PermissionDataProvider;
use App\Service\UserFullDataProvider;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class PostVoter extends Voter implements VoterInterface
{
    private const PERMISSIONS = [
        'create' => Permissions::POST_CREATE,
        'delete' => Permissions::POST_DELETE,
        'edit' => Permissions::POST_EDIT,
    ];

    private Permission $permission;

    public function __construct(
        private readonly UserFullDataProvider   $userProvider,
        private readonly PermissionDataProvider $permissionProvider,
        private readonly OwnerChecker           $ownerChecker
    ) {}

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

        $this->permission = $this->permissionProvider->getPermissionByName($attribute);
        $this->userProvider->setUser($user);

        return match ($attribute) {
            self::PERMISSIONS['delete'] => $this->canDeletePost($user, $subject),
            self::PERMISSIONS['edit'] => $this->canEditPost($user, $subject),
            default => $this->userProvider->hasPermission($this->permission),
        };
    }

    private function canDeletePost(User $user, Post $post): bool
    {
        if ($user->getRole()->getName() === Roles::ROLE_ADMIN) {
            return true;
        }

        if (!$this->ownerChecker->isOwner($user, $post)) {
            return false;
        }

        return $this->userProvider->hasPermission($this->permission);
    }

    private function canEditPost(User $user, Post $post): bool
    {
        if (!$this->ownerChecker->isOwner($user, $post)) {
            return false;
        }

        return $this->userProvider->hasPermission($this->permission);
    }
}
