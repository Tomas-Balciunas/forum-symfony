<?php

namespace App\Entity\Interface;

use App\Entity\User;

interface PostInterface {
    public function getAuthor(): ?User;
    public function setAuthor(User $author): void;
}