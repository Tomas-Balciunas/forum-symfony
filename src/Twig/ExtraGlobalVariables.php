<?php

namespace App\Twig;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ExtraGlobalVariables extends AbstractExtension
{
    public function __construct(private readonly Security $security, private RequestStack $requestStack)
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

        if (!$user && !$user instanceof User) {
            return false;
        }

        $latest = $user->getLatestNotification($user);
        $lastSeen = $this->requestStack->getSession()->get('last_seen');

        if ($latest && $lastSeen) {
            return $latest->getCreatedAt() > $lastSeen;
        }

        return false;
    }
}