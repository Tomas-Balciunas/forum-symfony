<?php

namespace App\Service\Misc;

use App\Data\Messages;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final readonly class PermissionAuthorization
{
    public function __construct(
        private Messages $messages,
        private AuthorizationCheckerInterface $authorizationChecker
    ) {}

    public function permission(string $permission, mixed $subject = null): void
    {
        if (!$this->authorizationChecker->isGranted($permission, $subject)) {
            $errorMsg = $this->messages->getErrMsg($permission);

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

            $exception = new AccessDeniedException($errorMsg);
            $exception->setAttributes($role);

            throw $exception;
        }
    }
}