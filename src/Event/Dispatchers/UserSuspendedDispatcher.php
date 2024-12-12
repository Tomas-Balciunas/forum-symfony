<?php

namespace App\Event\Dispatchers;

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

    public function onSuspendedUser(UserSuspension $suspension): void
    {
        $event = new UserSuspendedEvent($suspension->getIssuedFor());
        $this->dispatcher->dispatch($event, UserSuspendedEvent::NAME);
    }
}