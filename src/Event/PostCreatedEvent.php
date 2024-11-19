<?php

namespace App\Event;

use App\Entity\Post;
use Symfony\Contracts\EventDispatcher\Event;

class PostCreatedEvent extends Event
{
    public const NAME = 'post.created';

    public function __construct(private readonly Post $post)
    {
    }

    public function getPost(): Post
    {
        return $this->post;
    }
}
