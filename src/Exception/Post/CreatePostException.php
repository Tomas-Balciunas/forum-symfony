<?php

namespace App\Exception\Post;

use App\Exception\ValidationExceptionInterface;

class CreatePostException extends \Exception implements ValidationExceptionInterface
{
    protected $message = 'Post creation failed';
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