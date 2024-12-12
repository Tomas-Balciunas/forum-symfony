<?php

namespace App\Validate;

interface ValidatorInterface
{
    public function getErrors(): array;
}