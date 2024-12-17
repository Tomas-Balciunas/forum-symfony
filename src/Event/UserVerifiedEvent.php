<?php

namespace App\Event;

use App\Entity\Verification;

class UserVerifiedEvent
{
    public const NAME = 'user.verified';

    public function __construct(private readonly Verification $verification)
    {
    }

    public function getVerification(): Verification
    {
        return $this->verification;
    }
}