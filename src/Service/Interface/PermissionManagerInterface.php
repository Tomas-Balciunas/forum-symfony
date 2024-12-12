<?php

namespace App\Service\Interface;

use App\Entity\Permission;

interface PermissionManagerInterface
{
    public function getPermissionByName(string $name): ?Permission;
}