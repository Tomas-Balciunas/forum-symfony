<?php

namespace App\Exception\Suspension;

use App\Exception\ValidationExceptionInterface;

class ModifySuspensionException extends \Exception implements ValidationExceptionInterface
{
    protected $message = 'User suspension update failed.';
    private array $errors;

    public function __construct(array $errors)
    {
        parent::__construct($this->message);
        $this->errors = $errors;
    }

    public function getExceptionErrors(): array
    {
        return $this->errors;
    }
}