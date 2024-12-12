<?php

namespace App\Validate\Suspension\Finite;

use App\Entity\User;

class FiniteSuspensionActions
{
    protected function isAlreadySuspended(User $user): bool
    {
        return $user->getSuspension() !== null;
    }

    protected function suspensionDurationIsNull(array $data): bool
    {
        return empty(implode($data));
    }
}