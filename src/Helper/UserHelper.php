<?php

namespace App\Helper;

use App\Entity\User;
use App\Entity\UserSettings;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserHelper
{
    public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly RoleRepository         $roleRepository,
    )
    {
    }

    public function setDefaultSettings(User $user): void
    {
        $settings = new UserSettings();
        $settings->setUser($user);
        $this->manager->persist($settings);
    }

    public function setDefaultUserRole(User $user): void
    {
        $role = $this->roleRepository->findOneBy(['name' => User::DEFAULT_ROLE]);
        $user->setRole($role);
    }

    public function grantDefaultUserPermissions(User $user): void
    {
        $permissions = $user->getRole()->getPermissions();
        $user->setPermissions($permissions);
    }

    public function setUserStatus(User $user, string $status): void
    {
        $user->setStatus($status);
    }
}