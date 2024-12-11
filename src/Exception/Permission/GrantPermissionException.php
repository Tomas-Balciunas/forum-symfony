<?php

namespace App\Exception\Permission;

use App\Exception\ValidationExceptionInterface;

class GrantPermissionException extends \Exception implements ValidationExceptionInterface
{
    protected $message = 'Unable to grant permission.';
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