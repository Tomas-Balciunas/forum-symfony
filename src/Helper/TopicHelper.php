<?php

namespace App\Helper;

use App\Entity\Board;
use App\Entity\DTO\TopicDTO;
use App\Entity\Topic;

class TopicHelper
{
    public function makeTopic(TopicDTO $dto): Topic
    {
        $topic = new Topic();
        $topic->setTitle($dto->title);
        $topic->setBody($dto->body);

        return $topic;
    }
}