<?php

namespace App\Exception\Suspension;

use App\Exception\ValidationExceptionInterface;

class SuspensionAlreadyExistsException extends \Exception implements ValidationExceptionInterface
{
    public function __construct(string $username)
    {
        $message = 'User ' . $username . ' is already suspended.';
        parent::__construct($message);
    }
}