<?php

namespace App\EventSubscriber;

use App\Data\Config;
use App\Event\TopicPrepareEvent;
use App\Helper\GeneralHelper;
use App\Repository\TopicRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final readonly class TopicSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TopicRepository $topicRepository,
        private GeneralHelper $helper
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TopicPrepareEvent::NAME => [
                ['topicOnCooldown']
            ],
        ];
    }


    public function topicOnCooldown(TopicPrepareEvent $event): void
    {
        $user = $event->getUser();
        $latestTopic = $this->topicRepository->findLatestUserTopics($user, 1);

        if (!empty($latestTopic)) {
            $createdAt = $latestTopic[0]->getCreatedAt();
            $dateWithInterval = $this->helper->getFormattedDate($createdAt, Config::TOPIC_CREATE_COOLDOWN);

            if ($dateWithInterval > new \DateTime('now')) {
                throw new AccessDeniedException('You have to wait before creating another topic.');
            }
        }
    }
}