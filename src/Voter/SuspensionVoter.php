<?php

namespace App\Voter;

use App\Data\Permissions;
use App\Entity\User;
use App\Entity\UserSuspension;
use App\Service\PermissionDataProvider;
use App\Service\UserFullDataProvider;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class SuspensionVoter extends Voter implements VoterInterface
{
    private const PERMISSIONS = [
        'ban' => Permissions::USER_BAN,
        'unban' => Permissions::USER_UNBAN,
        'modify' => Permissions::USER_BAN_MODIFY
    ];

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

        $permission = $this->permissionProvider->getPermissionByName($attribute);
        $this->userProvider->setUser($user);

        return $this->userProvider->hasPermission($permission);
    }
}
