<?php

namespace App\Service\Misc;

use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\TopicRepository;

class UserDataProvider
{
    protected User $user;

    public function __construct(protected PermissionDataProvider $permissionProvider, protected PostRepository $postRepository, protected TopicRepository $topicRepository)
    {
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function isSuspended(): bool
    {
        return $this->user->getSuspension() !== null;
    }

    public function getLatestPosts(): array
    {
        return $this->postRepository->findLatestUserPosts($this->user);
    }

    public function getLatestTopics(): array
    {
        return $this->topicRepository->findLatestUserTopics($this->user);
    }
}