<?php

namespace App\Service;

use App\Entity\Permission;
use App\Entity\User;
use App\Helper\PermissionHelper;
use App\Repository\PostRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;

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