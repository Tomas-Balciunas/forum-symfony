<?php

namespace App\Voter;

use App\Data\Permissions;
use App\Entity\Board;
use App\Entity\User;
use App\Service\Misc\PermissionDataProvider;
use App\Service\Misc\UserFullDataProvider;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class BoardVoter extends Voter implements VoterInterface
{
    private const PERMISSIONS = [
        'createTopic' => Permissions::TOPIC_CREATE,
        'createBoard' => Permissions::BOARD_CREATE,
        'delete' => Permissions::BOARD_DELETE,
        'edit' => Permissions::BOARD_EDIT,
    ];

    public function __construct(private readonly UserFullDataProvider       $userProvider,
                                private readonly PermissionDataProvider $permissionProvider,
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, self::PERMISSIONS, true)) {
            return false;
        }

        if ($attribute === Permissions::BOARD_CREATE) {
            return true;
        }

        if (!$subject instanceof Board) {
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
