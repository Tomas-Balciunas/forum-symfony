<?php

namespace App\Exception\Suspension;

use App\Exception\ValidationExceptionInterface;

class SuspensionPastDateException extends \Exception implements ValidationExceptionInterface
{
    protected $message = 'Past date chosen';
}