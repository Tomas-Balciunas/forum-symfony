<?php

namespace App\Voter;

use App\Data\Permissions;
use App\Data\Roles;
use App\Entity\Permission;
use App\Entity\Topic;
use App\Entity\User;
use App\Service\PermissionDataProvider;
use App\Service\UserDataProvider;
use App\Service\UserFullDataProvider;
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
    ];

    private Permission $permission;

    public function __construct(private readonly UserFullDataProvider       $userProvider,
                                private readonly PermissionDataProvider $permissionProvider)
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
            self::PERMISSIONS['setPublic'] => $this->canSetPublic($user, $subject)
        };
    }

    private function canViewProfile(#[CurrentUser] User $authUser, User $user): bool
    {
        if ($authUser->getRole()->getName() === Roles::ROLE_ADMIN) {
            return true;
        }

        if ($this->isOwner($authUser, $user)) {
            return true;
        }

        if ($user->isPrivate()) {
            return false;
        }

        return $this->userProvider->hasPermission($this->permission);
    }

    private function isOwner(#[CurrentUser] User $authUser, User $user): bool
    {
        return $authUser === $user;
    }

    private function canSetPrivate(#[CurrentUser] User $authUser, User $user): bool
    {
        if ($authUser->getRole()->getName() === Roles::ROLE_ADMIN) {
            return true;
        }

        if ($this->isOwner($authUser, $user)) {
            return true;
        }

        return $this->userProvider->hasPermission($this->permission);
    }

    private function canSetPublic(#[CurrentUser] User $authUser, User $user): bool
    {
        if ($this->isOwner($authUser, $user)) {
            return true;
        }

        return $this->userProvider->hasPermission($this->permission);
    }
}
