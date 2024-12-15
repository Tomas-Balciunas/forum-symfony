<?php

namespace App\Command;

use App\Entity\Topic;
use App\Repository\BoardRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'seed:topics',
    description: 'Create mock data.',
)]
class SeedTopicsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly UserRepository              $userRepository,
        private readonly BoardRepository             $boardRepository,
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

        $boards = $this->boardRepository->findAll();

        if (empty($boards)) {
            $io->error('Create boards first.');
            return Command::FAILURE;
        }

        $users = $this->userRepository->findAll();

        if (empty($users)) {
            $io->error('Create users first.');
            return Command::FAILURE;
        }

        foreach ($users as $user) {
            for ($i = 0; $i < rand(1,4); $i++) {
                $topic = new Topic();
                $topic->setTitle($faker->words(rand(2,7), true));
                $topic->setBody($faker->text);
                $topic->setAuthor($user);
                $topic->setBoard($boards[rand(0, count($boards)-1)]);
                $this->entityManager->persist($topic);
            }

            $io->success("{$i} Created topics for user {$user->getUsername()}");
        }
        $this->entityManager->flush();
        $this->entityManager->clear();

        return Command::SUCCESS;
    }
}
