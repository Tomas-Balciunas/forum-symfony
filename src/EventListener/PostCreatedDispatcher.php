<?php

namespace App\EventListener;

use App\Entity\Post;
use App\Event\PostCreatedEvent;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsEntityListener(event: Events::postPersist, method: 'onPostPersist', entity: Post::class)]
readonly class PostCreatedDispatcher
{
    public function __construct(private EventDispatcherInterface $dispatcher)
    {
    }

    public function onPostPersist(Post $post): void
    {
        $event = new PostCreatedEvent($post);
        $this->dispatcher->dispatch($event, PostCreatedEvent::NAME);
    }
}