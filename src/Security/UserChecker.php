<?php

namespace App\Security;

use App\Entity\User;
use App\Exception\UserIsSuspendedException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Cache\CacheInterface;

readonly class UserChecker implements UserCheckerInterface
{
    public function __construct()
    {
    }

    /**
     * @throws UserIsSuspendedException
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (null !== $user->getSuspension()) {
            throw new UserIsSuspendedException($user->getSuspension()->getReason(), $user->getSuspension()->getExpiresAt());
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        $cache = new FilesystemAdapter();
        $cache->get('last_seen', static function () use ($user) {
            return new \DateTime('now', new \DateTimeZone('UTC'));
        });
    }
}