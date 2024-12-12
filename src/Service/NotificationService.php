<?php

namespace App\Service;

use App\Entity\User;
use App\Service\Misc\CacheHandler;

class NotificationService
{
    public function handleFetchNotifications(User $user)
    {
        $cache = new CacheHandler();
        $timeNow = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $lastSeen = $cache->getItemOrUpdate('last_seen', $timeNow)->get();

        $unread = $user->getUnreadNotifications($lastSeen);
        $read = $cache->getItemOrUpdate('past_notifications', function () use ($user, $lastSeen) {
            return $user->getReadNotifications($lastSeen);
        })->get();

        $cache->updateItem('last_seen', $timeNow);

        if (!$unread->isEmpty()) {
            $data = $user->getReadNotifications($timeNow);
            $cache->updateItem('past_notifications', $data);
        }

        return [$unread, $read];
    }
}