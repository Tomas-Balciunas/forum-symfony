<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Misc\NotificationTemplating;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class NotificationController extends AbstractController
{
    #[Route('/notifications', name: 'notifications')]
    public function index(#[CurrentUser] User $user, NotificationTemplating $helper, NotificationService $service): Response
    {
        [$unread, $read] = $service->handleFetchNotifications($user);

        return $this->render('notification/index.html.twig', [
            'read' => $read,
            'unread' => $unread,
            'helper' => $helper,
        ]);
    }
}
