<?php

namespace App\EventListener;

use App\Entity\User;
use App\Event\UserCreatedEvent;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsEntityListener(event: Events::postPersist, method: 'onUserPersist', entity: User::class)]
readonly class UserCreatedDispatcher
{
    public function __construct(private EventDispatcherInterface $dispatcher)
    {
    }

    public function onUserPersist(User $user): void
    {
        $event = new UserCreatedEvent($user);
        $this->dispatcher->dispatch($event, UserCreatedEvent::NAME);
    }
}