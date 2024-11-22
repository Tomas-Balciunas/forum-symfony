<?php

namespace App\Event;

use App\Entity\User;

class UserCreatedEvent
{
    public const NAME = 'user.created';

    public function __construct(private readonly User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}