<?php

namespace App\Service\Misc;

use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NotificationTemplating
{
    private array $formatted = [];

    public function __construct(
        private readonly UserRepository  $userRepository,
        private readonly TopicRepository $topicRepository,
        private readonly UrlGeneratorInterface $urlGenerator
    )
    {
    }

    public function getFormattedNotifications(Collection $data): array
    {
        $sorted = [
            'TOPIC_REPLY' => $data->filter(fn($notification) => $notification->getTemplate() === 'TOPIC_REPLY'),
        ];

        foreach ($sorted as $template => $type) {
            match ($template) {
                'TOPIC_REPLY' => $this->topicReply($type),
                default => [],
            };
        }

        return $this->formatted;
    }

    private function topicReply(Collection $data): void
    {
        $userIds = $data->map(fn($n) => $n->getData()['user'])->toArray();
        $topicIds = $data->map(fn($n) => $n->getData()['topic'])->toArray();

        $userIds = array_unique($userIds);
        $topicIds = array_unique($topicIds);

        $users = $this->userRepository->findBy(['id' => $userIds]);
        $topics = $this->topicRepository->findBy(['id' => $topicIds]);

        $usersMap = [];
        foreach ($users as $user) {
            $usersMap[$user->getId()] = $user;
        }

        $topicsMap = [];
        foreach ($topics as $topic) {
            $topicsMap[$topic->getId()] = $topic;
        }

        foreach ($data as $notification) {
            $topicId = $notification->getData()['topic'];
            $userId = $notification->getData()['user'];
            $postId = $notification->getData()['post'];
// TODO: twig
            $this->formatted[$notification->getId()]['html'] = sprintf(
                '<a href="%s" class="text-yellow-400 hover:underline">%s</a> has replied to your topic <a href="%s" class="text-yellow-400 hover:underline">%s</a>',
                $this->urlGenerator->generate('user_profile_public', ['id' => $userId]),
                $usersMap[$userId]->getUsername(),
                $this->urlGenerator->generate('post_goto', [
                    'id' => $postId,
                ]),
                $topicsMap[$topicId]->getTitle()
            );
            $this->formatted[$notification->getId()]['createdAt'] = $notification->getCreatedAt();
        }
    }
}