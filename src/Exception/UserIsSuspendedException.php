<?php

namespace App\Exception;


use DateTime;

class UserIsSuspendedException extends \Exception
{
    protected $message = 'This account has been suspended.';
    private string $reason;
    private DateTime $expiresAt;

    public function __construct(string $reason, DateTime $expiresAt = null)
    {
        parent::__construct($this->message);
        $this->reason = $reason;
        $this->expiresAt = $expiresAt;
    }

    public function getExpiresAt(): DateTime
    {
        return $this->expiresAt;
    }

    public function getReason(): string
    {
        return $this->reason;
    }
}