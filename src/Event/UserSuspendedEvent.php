<?php

namespace App\Event;

use App\Entity\UserSuspension;

class UserSuspendedEvent
{
    public const NAME = 'user.suspended';

    public function __construct(private readonly UserSuspension $userSuspension)
    {
    }

    public function getSuspension(): UserSuspension
    {
        return $this->userSuspension;
    }
}