<?php

namespace App\Validate\Post\Create;

use App\Data\Config;
use App\Entity\Topic;
use App\Entity\User;
use App\Helper\GeneralHelper;
use App\Repository\PostRepository;

class CreatePostActions
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly GeneralHelper  $helper,
    )
    {
    }

    protected function isTopicLocked(Topic $topic): bool
    {
        return $topic->getIsLocked();
    }

    public function isPostOnCooldown(User $user): bool
    {
        $latestPost = $this->postRepository->findLatestUserPosts($user, 1);

        if (!empty($latestPost)) {
            $createdAt = $latestPost[0]->getCreatedAt();
            $dateWithInterval = $this->helper->getFormattedDate($createdAt, Config::POST_CREATE_COOLDOWN);

            return $dateWithInterval > new \DateTime('now');
        }

        return false;
    }
}