<?php

namespace App\Command;

use App\Data\Roles;
use App\Entity\Board;
use App\Entity\Permission;
use App\Entity\Post;
use App\Entity\Role;
use App\Entity\Topic;
use App\Entity\User;
use App\Helper\PermissionHelper;
use App\Repository\BoardRepository;
use App\Repository\PermissionRepository;
use App\Repository\RoleRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'seed:boards',
    description: 'Create mock data.',
)]
class SeedBoardsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface      $entityManager
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $board = new Board();
            $board->setTitle($faker->words(rand(1, 10), true));
            $board->setDescription($faker->sentence(rand(5, 25)));
            $this->entityManager->persist($board);
        }

        $this->entityManager->flush();
        $this->entityManager->clear();

        $io->success('Boards created successfully.');

        return Command::SUCCESS;
    }
}
