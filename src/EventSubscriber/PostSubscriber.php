<?php

namespace App\EventSubscriber;

use App\Entity\Notification;
use App\Event\PostCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class PostSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $manager
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PostCreatedEvent::NAME => [
                ["userPostCount"],
                ['sendNotification']
            ]
        ];
    }


    public function userPostCount(PostCreatedEvent $event): void
    {
        $post = $event->getPost();
        $post->getAuthor()->incrementPostCount();
        $this->manager->flush();
    }

    public function sendNotification(PostCreatedEvent $event): void
    {
        $topic = $event->getPost()->getTopic();
        $author = $event->getPost()->getAuthor();
        $recipient = $topic->getAuthor();

        if ($author === $recipient) {
            return;
        }

        $data = [
            'user' => $author->getId(),
            'topic' => $topic->getId(),
            'post' => $event->getPost()->getId(),
        ];

        $template = 'TOPIC_REPLY';

        $notification = new Notification();
        $notification->setTemplate($template);
        $notification->setData($data);
        $notification->setUser($recipient);

        $this->manager->persist($notification);
        $this->manager->flush();
    }
}