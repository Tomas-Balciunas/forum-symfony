<?php

namespace App\Helper;

use App\Repository\PostRepository;

readonly class PostHelper
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    public function getPostPosition(int $postId, int $topicId): int
    {
        $pos = $this->postRepository->findPostPosInTopic($postId, $topicId);

        return ceil($pos / 10);
    }
}