<?php

namespace App\Entity\Interface;

use App\Entity\User;

interface AuthorInterface {
    public function getAuthor(): ?User;
    public function setAuthor(User $author): void;
}