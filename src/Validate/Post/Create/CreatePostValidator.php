<?php

namespace App\Validate\Post\Create;


use App\Entity\Topic;
use App\Entity\User;
use App\Validate\ValidatorInterface;

class CreatePostValidator extends CreatePostActions implements ValidatorInterface
{
    private array $errors = [];

    public function validate(Topic $topic, User $user): static
    {
        if ($this->isTopicLocked($topic)) {
            $this->errors[] = "Topic is locked";
        }

        if ($this->isPostOnCooldown($user)) {
            $this->errors[] = "You must wait before posting again";
        }

        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}