<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\EventDispatcher\Event;

class PostPrepareEvent extends Event
{
    public const NAME = 'post.pending';

    public function __construct(#[CurrentUser] private readonly User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
