<?php

namespace App\Validate\Suspension\Permanent;

use App\Entity\User;

class PermanentSuspensionActions
{
    protected function isAlreadySuspended(User $user): bool
    {
        return $user->getSuspension() !== null;
    }
}