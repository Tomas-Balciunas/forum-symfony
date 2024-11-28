<?php

namespace App\Exception\Suspension;

use App\Exception\ValidationExceptionInterface;

class SuspensionNullTimeException extends \Exception implements ValidationExceptionInterface
{
    protected $message = 'Suspension duration is missing. ';
}