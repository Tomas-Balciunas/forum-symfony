<?php

namespace App\Exception;

interface ValidationExceptionInterface
{
    public function getExceptionErrors(): array;
}