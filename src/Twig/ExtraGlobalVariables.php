<?php

namespace App\Twig;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Cache\CacheInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ExtraGlobalVariables extends AbstractExtension
{
    public function __construct(private readonly Security $security)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('notifications', [$this, 'checkNotifications']),
        ];
    }

    public function checkNotifications(): bool
    {
        $user = $this->security->getUser();
        $cache = new FilesystemAdapter();

        if (!$user && !$user instanceof User) {
            return false;
        }

        $latest = $user->getLatestNotification($user);
        $lastSeen = $cache->getItem('last_seen');

        if ($latest && $lastSeen->isHit()) {
            return $latest->getCreatedAt() > $lastSeen->get();
        }

        return false;
    }
}