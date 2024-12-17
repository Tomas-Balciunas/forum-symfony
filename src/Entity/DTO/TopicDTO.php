<?php

namespace App\Entity\DTO;

use App\Entity\Board;
use App\Entity\User;
use App\Service\Misc\HydrateTrait;
use Doctrine\Common\Collections\Collection;

class TopicDTO
{
    use HydrateTrait;

    public ?int $id = null;

    public ?string $title = null;

    public ?string $body = null;

    public ?bool $isLocked = null;
    public ?bool $isVisible = null;
    public ?bool $isImportant = null;

    public ?\DateTimeImmutable $createdAt = null;

    public Collection $posts;

    public ?User $author = null;

    public ?Board $board = null;

}
