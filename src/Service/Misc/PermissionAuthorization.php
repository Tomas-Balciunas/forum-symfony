<?php

namespace App\Service\Misc;

use App\Data\Messages;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final readonly class PermissionAuthorization
{
    public function __construct(
        private Messages $messages,
        private AuthorizationCheckerInterface $authorizationChecker,
        private LoggerInterface $logger,
        private Security $security,
    ) {}

    public function permission(string $permission, mixed $subject = null): void
    {
        if (!$this->authorizationChecker->isGranted($permission, $subject)) {
            $errorMsg = $this->messages->getErrMsg($permission);
            $this->log($permission);

            $exception = new AccessDeniedException($errorMsg);
            $exception->setAttributes($permission);
            $exception->setSubject($subject);

            throw $exception;
        }
    }

    public function role(string $role): void
    {
        if (!$this->authorizationChecker->isGranted($role)) {
            $errorMsg = 'Topic creation in this board is restricted.';
            $this->logRole($role);

            $exception = new AccessDeniedException($errorMsg);
            $exception->setAttributes($role);

            throw $exception;
        }
    }

    private function log(string $permission): void
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return;
        }

        $this->logger->warning('Access denied: user {userId} was denied permission to: {permission}. Reason: permission is not granted.', [
            'userId' => $user->getId() ?? 'unauthenticated',
            'permission' => $permission,
        ]);
    }

    private function logRole(string $role): void
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return;
        }

        $this->logger->warning('Access denied: user {userId} was denied access. Reason: role {userRole} is insufficient, required: {role}.', [
            'userId' => $user->getId() ?? 'unauthenticated',
            'userRole' => $user->getRole()?->getName() ?? 'unauthenticated',
            'role' => $role,
        ]);
    }
}