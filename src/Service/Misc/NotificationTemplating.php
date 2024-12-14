<?php

namespace App\Service\Misc;

use App\Helper\PostHelper;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

readonly class NotificationTemplating
{
    public function __construct(
        private UserRepository        $userRepository,
        private TopicRepository       $topicRepository,
        private UrlGeneratorInterface $urlGenerator
    )
    {
    }

    public function getFormattedNotifications(Collection $data): array
    {
    //TODO: fix this
        return $this->topicReply($data);
    }

    private function topicReply(Collection $data): array
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

        $formattedData = [];

        foreach ($data as $key => $notification) {
            $topicId = $notification->getData()['topic'];
            $userId = $notification->getData()['user'];
            $postId = $notification->getData()['post'];

            $formattedData[$key]['html'] = sprintf(
                '<a href="%s" class="text-yellow-400 hover:underline">%s</a> has replied to your topic <a href="%s" class="text-yellow-400 hover:underline">%s</a>',
                $this->urlGenerator->generate('user_profile_public', ['id' => $userId]),
                $usersMap[$userId]->getUsername(),
                $this->urlGenerator->generate('post_goto', [
                    'id' => $postId,
                ]),
                $topicsMap[$topicId]->getTitle()
            );
            $formattedData[$key]['createdAt'] = $notification->getCreatedAt();
        }

        return $formattedData;
    }
}