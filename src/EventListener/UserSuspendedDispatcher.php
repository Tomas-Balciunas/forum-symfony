<?php

namespace App\EventListener;

use App\Entity\UserSuspension;
use App\Event\UserSuspendedEvent;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsEntityListener(event: Events::postPersist, method: 'onSuspendedUser', entity: UserSuspension::class)]
readonly class UserSuspendedDispatcher
{
    public function __construct(private EventDispatcherInterface $dispatcher)
    {
    }

    public function onSuspendedUser(UserSuspension $userSuspension): void
    {
        $event = new UserSuspendedEvent($userSuspension);
        $this->dispatcher->dispatch($event, UserSuspendedEvent::NAME);
    }
}