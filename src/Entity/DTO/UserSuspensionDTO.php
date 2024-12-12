<?php

namespace App\Entity\DTO;

use App\Entity\User;
use App\Service\Misc\HydrateTrait;
use DateTime;

class UserSuspensionDTO
{
    use HydrateTrait;

    public ?int $id = null;

    public ?string $reason = null;

    public ?DateTime $expiresAt = null;

    public ?bool $isPermanent = null;

    public ?User $issuedFor = null;

    public ?User $issuedBy = null;

    public ?\DateTimeImmutable $issuedAt = null;
}