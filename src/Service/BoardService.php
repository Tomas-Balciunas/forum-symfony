<?php

namespace App\Service;

use App\Entity\Board;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;

class BoardService
{
    public function __construct(private readonly EntityManagerInterface $manager, private RoleRepository $roleRepository)
    {
    }

    public function handleCreateBoard(Board $board, string $role): void
    {
        $role = $this->roleRepository->findOneBy(['name' => $role]);
        $board->setAccess($role);

        $this->manager->persist($board);
        $this->manager->flush();
    }

    public function handleUpdateBoard(Board $board, string $role): void
    {
        $role = $this->roleRepository->findOneBy(['name' => $role]);
        $board->setAccess($role);
        $this->manager->flush();
    }
}