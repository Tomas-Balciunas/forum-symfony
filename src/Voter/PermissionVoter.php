<?php

namespace App\Voter;

use App\Data\Permissions;
use App\Entity\Permission;
use App\Entity\User;
use App\Service\Misc\OwnerChecker;
use App\Service\PermissionDataProvider;
use App\Service\UserFullDataProvider;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class PermissionVoter extends Voter implements VoterInterface
{
    private const PERMISSIONS = [
        'grant' => Permissions::USER_ADD_PERMISSION,
        'revoke' => Permissions::USER_REVOKE_PERMISSION,
    ];

    private Permission $permission;

    public function __construct(
        private readonly UserFullDataProvider   $userProvider,
        private readonly PermissionDataProvider $permissionProvider
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, $this::PERMISSIONS, true)) {
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

        return $this->userProvider->hasPermission($this->permission);
    }
}
