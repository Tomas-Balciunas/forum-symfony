<?php

namespace App\Validate\Post\Create;


use App\Entity\Topic;
use App\Validate\ValidatorInterface;

class CreatePostValidator extends CreatePostActions implements ValidatorInterface
{
    private array $errors = [];

    public function validate(Topic $topic): static
    {
        if ($this->isTopicLocked($topic)) {
            $this->errors[] = "Topic is locked";
        }

        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}