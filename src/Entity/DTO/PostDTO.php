<?php

namespace App\Entity\DTO;

use App\Service\Misc\HydrateTrait;

class PostDTO
{
    use HydrateTrait;

    public ?string $body = null;
}