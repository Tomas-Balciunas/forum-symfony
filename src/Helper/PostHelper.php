<?php

namespace App\Helper;

use App\Entity\DTO\PostDTO;
use App\Entity\Post;
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

    public function makePost(PostDTO $dto): Post
    {
        $post = new Post();
        $post->setBody($dto->body);

        return $post;
    }

    public function updatePost(PostDTO $dto, Post $post): Post
    {
        $post->setBody($dto->body);

        return $post;
    }
}