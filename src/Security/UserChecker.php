<?php

namespace App\Security;

use App\Entity\User;
use App\Exception\UserIsSuspendedException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Cache\CacheInterface;

readonly class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        // empty
    }

    /**
     * @throws UserIsSuspendedException
     * @throws InvalidArgumentException
     */
    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (null !== $user->getSuspension()) {
            throw new UserIsSuspendedException($user->getSuspension()->getReason(), $user->getSuspension()->getExpiresAt());
        }

        $cache = new FilesystemAdapter();
        $cache->get('last_seen_' . $user->getId(), static function () {
            return new \DateTimeImmutable('now');
        });
    }
}