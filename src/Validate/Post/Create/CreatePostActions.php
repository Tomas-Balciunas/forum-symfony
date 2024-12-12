<?php

namespace App\Validate\Post\Create;

use App\Entity\Topic;

class CreatePostActions
{
    public function __construct()
    {
    }

    protected function isTopicLocked(Topic $topic): bool
    {
        return $topic->isLocked();
    }
}