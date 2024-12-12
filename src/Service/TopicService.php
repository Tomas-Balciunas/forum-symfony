<?php

namespace App\Service;

use App\Entity\Board;
use App\Entity\DTO\TopicDTO;
use App\Entity\Topic;
use App\Entity\User;
use App\Helper\TopicHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

readonly class TopicService
{
    public function __construct(
        private EntityManagerInterface $manager,
        private TopicHelper $helper,
    ) {}

    public function handleCreateTopic(TopicDTO $dto, Board $board, #[CurrentUser] User $user): void
    {
        $topic = $this->helper->makeTopic($dto);
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