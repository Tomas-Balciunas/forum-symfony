<?php

namespace App\EventListener;

use App\Event\UserSuspendedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class UserSuspendedSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserSuspendedEvent::NAME => ['setUserStatus'],
        ];
    }

    public function setUserStatus(UserSuspendedEvent $event): void
    {
        $suspension = $event->getSuspension();
        $issuedFor = $suspension->getIssuedFor();

        $issuedFor->setStatus('suspended');
        $this->entityManager->flush();
    }
}