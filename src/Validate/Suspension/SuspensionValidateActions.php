<?php

namespace App\Validate\Suspension;

use App\Entity\User;
use App\Exception\Suspension\SuspensionAlreadyExistsException;
use App\Exception\Suspension\SuspensionNullTimeException;
use App\Exception\Suspension\SuspensionPastDateException;

class SuspensionValidateActions
{
    protected static function isAlreadySuspended(User $user): void
    {
        if ($user->getSuspension() !== null) {
            throw new SuspensionAlreadyExistsException($user->getUsername());
        }
    }

    protected static function suspensionDateNotPast($date, $isPermanent): void
    {
        if ($date < new \DateTime('now') && !$isPermanent) {
            throw new SuspensionPastDateException();
        }
    }

    protected static function suspensionDurationNotNull(array $data): void
    {
        if (empty(implode($data))) {
            throw new SuspensionNullTimeException();
        }
    }
}