<?php

namespace App\Service;

use App\Entity\Board;
use App\Entity\Topic;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

readonly class TopicService
{
    public function __construct(
        private EntityManagerInterface $manager
    ) {}

    public function handleCreateTopic(Topic $topic, Board $board, #[CurrentUser] User $user): void
    {
        $topic->setAuthor($user);
        $board->addTopic($topic);
        $this->manager->flush();
    }

    public function handleMoveTopic(Topic $topic, Board $board): void
    {
        $topic->setBoard($board);
        $this->manager->flush();
    }
}