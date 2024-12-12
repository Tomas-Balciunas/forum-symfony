<?php

namespace App\Entity\DTO;

use DateTime;

class SuspensionModifyDTO
{
    public ?string $reason;

    public ?DateTime $expiresAt;

    public ?bool $isPermanent;

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function getExpiresAt(): ?DateTime
    {
        return $this->expiresAt;
    }

    public function getIsPermanent(): ?bool
    {
        return $this->isPermanent;
    }

    public function setReason(?string $reason): void
    {
        $this->reason = $reason;
    }

    public function setExpiresAt(?DateTime $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    public function setIsPermanent(?bool $isPermanent): void
    {
        $this->isPermanent = $isPermanent;
    }
}