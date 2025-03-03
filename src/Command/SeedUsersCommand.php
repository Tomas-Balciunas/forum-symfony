<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'seed:users',
    description: 'Create mock data.',
)]
class SeedUsersCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly UserPasswordHasherInterface $hasher,
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

        for ($i = 0; $i < 20; $i++)
        {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setUsername($faker->userName);
            $user->setPassword($this->hasher->hashPassword($user, 'password'));
            $this->entityManager->persist($user);

            $io->success("{$i} Created user {$user->getUsername()}");
        }

        $this->entityManager->flush();
        $this->entityManager->clear();

        return Command::SUCCESS;
    }
}
