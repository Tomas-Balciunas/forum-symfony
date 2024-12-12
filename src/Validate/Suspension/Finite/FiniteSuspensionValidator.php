<?php

namespace App\Validate\Suspension\Finite;

use App\Entity\User;
use App\Validate\ValidatorInterface;

class FiniteSuspensionValidator extends FiniteSuspensionActions implements ValidatorInterface
{
    private array $errors = [];

    public function validate(array $data, User $user): static
    {
        if ($this->isAlreadySuspended($user)) {
            $this->errors[] = "User is already suspended, modify suspension to make adjustments.";
        }
        if ($this->suspensionDurationIsNull($data)) {
            $this->errors[] = "Suspension duration is required.";
        }

        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}