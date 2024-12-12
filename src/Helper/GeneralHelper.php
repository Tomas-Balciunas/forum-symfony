<?php

namespace App\Helper;

class GeneralHelper
{
    public function getFormattedDate($latest, int $minutes = 5): \DateTime
    {
        $createdAt = \DateTime::createFromImmutable($latest);
        $createdAt->add(new \DateInterval('PT' . $minutes . 'M'));

        return $createdAt;
    }
}