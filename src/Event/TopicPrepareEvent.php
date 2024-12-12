<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\EventDispatcher\Event;

class TopicPrepareEvent extends Event
{
    public const NAME = 'topic.pending';

    public function __construct(#[CurrentUser] private readonly User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
