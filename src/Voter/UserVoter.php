<?php

namespace App\Voter;

use App\Data\Permissions;
use App\Data\Roles;
use App\Entity\Permission;
use App\Entity\User;
use App\Service\Misc\OwnerChecker;
use App\Service\Misc\PermissionDataProvider;
use App\Service\Misc\UserFullDataProvider;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UserVoter extends Voter implements VoterInterface
{
    private const PERMISSIONS = [
        'viewProfile' => Permissions::USER_VIEW_PROFILE,
        'setPrivate' => Permissions::USER_SET_PRIVATE,
        'setPublic' => Permissions::USER_SET_PUBLIC,
        'viewPosts' => Permissions::MISC_VIEW_USER_POSTS,
        'viewTopics' => Permissions::MISC_VIEW_USER_TOPICS,
    ];

    private Permission|null $permission;

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

        if (!$subject instanceof User) {
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
            self::PERMISSIONS['viewProfile'] => $this->canViewProfile($user, $subject),
            self::PERMISSIONS['setPrivate'] => $this->canSetPrivate($user, $subject),
            self::PERMISSIONS['setPublic'] => $this->canSetPublic($user, $subject),
            self::PERMISSIONS['viewPosts'] => $this->canViewTopics($user, $subject),
            self::PERMISSIONS['viewTopics'] => $this->canViewPosts($user, $subject),
            default => $this->userProvider->hasPermission($this->permission),
        };
    }

    private function canViewProfile(#[CurrentUser] User $authUser, User $user): bool
    {
        if ($authUser->getRole()->getName() === Roles::ROLE_ADMIN) {
            return true;
        }

        if ($this->ownerChecker->isOwner($authUser, $user)) {
            return true;
        }

        if ($user->isPrivate()) {
            return false;
        }

        return $this->userProvider->hasPermission($this->permission);
    }

    private function canSetPrivate(#[CurrentUser] User $authUser, User $user): bool
    {
        if ($authUser->getRole()->getName() === Roles::ROLE_ADMIN) {
            return true;
        }

        if ($this->ownerChecker->isOwner($authUser, $user)) {
            return true;
        }

        return $this->userProvider->hasPermission($this->permission);
    }

    private function canSetPublic(#[CurrentUser] User $authUser, User $user): bool
    {
        if ($this->ownerChecker->isOwner($authUser, $user)) {
            return true;
        }

        return $this->userProvider->hasPermission($this->permission);
    }

    public function canViewPosts(#[CurrentUser] User $authUser, User $user): bool
    {
        if ($this->ownerChecker->isOwner($authUser, $user)) {
            return true;
        }

        return $user->getSettings()->isShowPosts();
    }

    public function canViewTopics(#[CurrentUser] User $authUser, User $user): bool
    {
        if ($this->ownerChecker->isOwner($authUser, $user)) {
            return true;
        }

        return $user->getSettings()->isShowTopics();
    }
}
