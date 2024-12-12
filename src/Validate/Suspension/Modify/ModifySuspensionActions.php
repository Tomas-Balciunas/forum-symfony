<?php

namespace App\Validate\Suspension\Modify;

class ModifySuspensionActions
{
    protected function suspensionDateIsPast($date): bool
    {
        return $date < new \DateTime('now', new \DateTimeZone('UTC'));
    }
}