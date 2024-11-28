<?php

namespace App\Validate\Suspension;

use App\Entity\DTO\SuspensionModifyDTO;
use App\Entity\User;

class SuspensionValidator extends SuspensionValidateActions
{
    public static function validateSuspend(array $data, User $user): void
    {
        self::isAlreadySuspended($user);
        self::suspensionDurationNotNull($data);
    }

    public static function validateSuspendPermanently(User $user): void
    {
        self::isAlreadySuspended($user);
    }

    public static function validateModifySuspension(SuspensionModifyDTO $dto): void
    {
        self::suspensionDateNotPast($dto->getExpiresAt(), $dto->getIsPermanent());
    }
}