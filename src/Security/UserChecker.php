<?php

namespace App\Security;

use App\Entity\User;
use App\Exception\UserIsSuspendedException;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Cache\CacheInterface;

readonly class UserChecker implements UserCheckerInterface
{
    public function __construct(private LoggerInterface $logger)
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

        $suspension = $user->getSuspension();

        if (null !== $suspension) {
            $this->logger->warning('Suspended user {userId} attempted to authenticate.', [
                'userId' => $user->getId(),
                'suspensionId' => $suspension->getId(),
                'reason' => $suspension->getReason(),
                'expiresAt' => $suspension->getExpiresAt(),
                'isPermanent' => $suspension->getIsPermanent()
            ]);

            throw new UserIsSuspendedException($suspension->getReason(), $suspension->getExpiresAt());
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        $cache = new FilesystemAdapter();
        $cache->get('last_seen_' . $user->getId(), static function () {
            return new \DateTimeImmutable('now');
        });
    }
}