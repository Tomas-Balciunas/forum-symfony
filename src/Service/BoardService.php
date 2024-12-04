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

    public function handleCreateBoard(Board $board, string $access): void
    {
        $role = $this->roleRepository->findOneBy(['name' => $access]);
        $board->setAccess($role);

        $this->manager->persist($board);
        $this->manager->flush();
    }

    public function handleUpdateBoard(Board $board, string $access): void
    {
        $role = $this->roleRepository->findOneBy(['name' => $access]);
        $board->setAccess($role);
        $this->manager->flush();
    }
}