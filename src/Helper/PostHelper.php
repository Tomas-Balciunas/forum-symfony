<?php

namespace App\Helper;

use App\Data\Config;
use App\Entity\DTO\PostDTO;
use App\Entity\Post;

readonly class PostHelper
{
    public function getPostPosition(int $position): int
    {
        return ceil($position / Config::PAGE_SIZE);
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