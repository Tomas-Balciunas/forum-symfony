<?php

namespace App\Validate\Suspension\Modify;

use App\Entity\DTO\SuspensionModifyDTO;
use App\Entity\User;
use App\Exception\ValidationExceptionInterface;
use App\Validate\ValidatorInterface;

class ModifySuspensionValidator extends ModifySuspensionActions implements ValidatorInterface
{
    private array $errors = [];

    public function validate(\DateTime $date): static
    {
        if ($this->suspensionDateIsPast($date)) {
            $this->errors[] = "Suspension date is past.";
        }

        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}