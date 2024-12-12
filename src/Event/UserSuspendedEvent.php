<?php

namespace App\Event;

use App\Entity\User;

class UserSuspendedEvent
{
    public const NAME = 'user.suspended';

    public function __construct(private readonly User $issuedFor)
    {
    }

    public function getUser(): User
    {
        return $this->issuedFor;
    }
}