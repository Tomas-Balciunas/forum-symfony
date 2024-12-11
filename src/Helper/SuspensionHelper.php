<?php

namespace App\Helper;

use App\Entity\DTO\SuspensionModifyDTO;
use App\Entity\User;
use App\Entity\UserSuspension;
use DateInterval;
use DateTime;
use DateTimeZone;

class SuspensionHelper
{
    public function makeSuspensionFoundation(User $issuedBy, User $issuedFor, string $reason): UserSuspension
    {
        $suspension = new UserSuspension();
        $suspension->setIssuedBy($issuedBy);
        $suspension->setIssuedFor($issuedFor);
        $suspension->setReason($reason);

        return $suspension;
    }

    public function createSuspensionModifyDto(UserSuspension $suspension): SuspensionModifyDto
    {
        $dto = new SuspensionModifyDTO();
        $dto->setReason($suspension->getReason());
        $dto->setExpiresAt($suspension->getExpiresAt());
        $dto->setIsPermanent($suspension->isPermanent());

        return $dto;
    }

    public function makeSuspensionDate(
        $days,
        $hours,
    ): DateTime
    {
        $interval = new DateInterval("P{$days}DT{$hours}H");
        $until = new DateTime('now', new DateTimeZone('UTC'));

        return $until->add($interval);
    }
}