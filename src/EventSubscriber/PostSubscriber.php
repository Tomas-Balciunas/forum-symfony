<?php

namespace App\EventSubscriber;

use App\Data\Config;
use App\Entity\Notification;
use App\Event\PostCreatedEvent;
use App\Event\PostPrepareEvent;
use App\Helper\GeneralHelper;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final readonly class PostSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $manager,
        private PostRepository $postRepository,
        private GeneralHelper $helper
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PostCreatedEvent::NAME => [
                ["userPostCount"],
                ['sendNotification']
            ],
            PostPrepareEvent::NAME => [
                ['postOnCooldown']
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

    public function postOnCooldown(PostPrepareEvent $event): void
    {
        $user = $event->getUser();
        $latestPost = $this->postRepository->findLatestUserPosts($user, 1);

        if (!empty($latestPost)) {
            $createdAt = $latestPost[0]->getCreatedAt();
            $dateWithInterval = $this->helper->getFormattedDate($createdAt, Config::POST_CREATE_COOLDOWN);

            if ($dateWithInterval > new \DateTime('now')) {
                throw new AccessDeniedException('You have to wait before posting again.');
            }
        }
    }
}