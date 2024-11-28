<?php

namespace App\Exception\Permission;

use App\Exception\ValidationExceptionInterface;

class PermissionAlreadyGrantedException extends \Exception implements ValidationExceptionInterface
{
    public function __construct(string $permission)
    {
        $message = 'User already has permission ' . $permission . '.';
        parent::__construct($message);
    }
}