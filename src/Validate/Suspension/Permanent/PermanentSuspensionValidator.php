<?php

namespace App\Validate\Suspension\Permanent;

use App\Entity\User;
use App\Validate\ValidatorInterface;

class PermanentSuspensionValidator extends PermanentSuspensionActions implements ValidatorInterface
{
    private array $errors = [];

    public  function validate(User $user): static
    {
        if ($this->isAlreadySuspended($user)) {
            $this->errors[] = "User is already suspended.";
        }

        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}