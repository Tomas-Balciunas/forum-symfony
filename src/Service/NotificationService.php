<?php

namespace App\Service;

use App\Entity\User;
use App\Service\Misc\CacheHandler;

class NotificationService
{
    public function handleFetchNotifications(User $user): array
    {
        $cache = new CacheHandler();
        $timeNow = new \DateTimeImmutable('now');
        $lastSeen = $cache->getItemOrUpdate('last_seen_' . $user->getId(), $timeNow)->get();

        $unread = $user->getUnreadNotifications($lastSeen);
        $read = $cache->getItemOrUpdate('past_notifications_' . $user->getId(), function () use ($user, $lastSeen) {
            return $user->getReadNotifications($lastSeen);
        })->get();

        $cache->updateItem('last_seen_' . $user->getId(), $timeNow);

        if (!$unread->isEmpty()) {
            $data = $user->getReadNotifications($timeNow);
            $cache->updateItem('past_notifications_' . $user->getId(), $data);
        }

        return [$unread, $read];
    }
}