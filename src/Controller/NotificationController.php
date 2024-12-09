<?php

namespace App\Controller;

use App\Data\Messages;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class NotificationController extends AbstractController
{
    #[Route('/notifications', name: 'notifications')]
    public function index(#[CurrentUser] User $user, Messages $helper, Request $request): Response
    {
        $cache = new FilesystemAdapter();
        $session = $request->getSession();
        $lastSeen = $session->get('last_seen');

        $unread = $user->getUnreadNotifications($lastSeen);
        $read = $cache->get('past_notifications', function () use ($lastSeen, $user) {
            return $user->getReadNotifications($lastSeen);
        });

        $session->set('last_seen', new \DateTimeImmutable('now', new \DateTimeZone('UTC')));

        return $this->render('notification/index.html.twig', [
            'read' => $read,
            'unread' => $unread,
            'helper' => $helper,
        ]);
    }
}
