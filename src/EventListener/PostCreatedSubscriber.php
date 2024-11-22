<?php

namespace App\EventListener;

use App\Event\PostCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class PostCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $manager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PostCreatedEvent::NAME => [
                "userPostCount",
            ]
        ];
    }


    public function userPostCount(PostCreatedEvent $event): void
    {
        $post = $event->getPost();
        $post->getAuthor()->incrementPostCount();
        $this->manager->flush();
    }
}