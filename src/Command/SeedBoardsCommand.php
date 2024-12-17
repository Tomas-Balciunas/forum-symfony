<?php

namespace App\Command;

use App\Entity\Board;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'seed:boards',
    description: 'Create mock data.',
)]
class SeedBoardsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly RoleRepository $roleRepository,
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
        $role = $this->roleRepository->findOneBy(['name' => 'ROLE_USER']);

        for ($i = 0; $i < 10; $i++) {
            $board = new Board();
            $board->setTitle($faker->words(rand(1, 10), true));
            $board->setDescription($faker->sentence(rand(5, 25)));
            $board->setAccess($role);
            $this->entityManager->persist($board);
        }

        $this->entityManager->flush();
        $this->entityManager->clear();

        $io->success('Boards created successfully.');

        return Command::SUCCESS;
    }
}
