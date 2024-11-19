<?php

namespace App\Entity\Interface;

use App\Entity\User;

interface TopicInterface {
    public function getAuthor(): ?User;
    public function setAuthor(User $author): void;
}