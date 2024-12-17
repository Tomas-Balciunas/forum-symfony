<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'seed:admin',
    description: 'Create admin account.',
)]
class SeedAdminCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly UserPasswordHasherInterface $hasher,
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

        $password = 'password';
        $role = $this->roleRepository->findOneBy(['name' => 'ROLE_ADMIN']);

        if (null === $role) {
            $io->error('Role not found. Make sure you ran seed:permissions command.');
            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail($faker->email);
        $user->setUsername($faker->userName);
        $user->setPassword($this->hasher->hashPassword($user, 'password'));
        $this->entityManager->persist($user);
        $user->setRole($role);
        $user->setPermissions($role->getPermissions());
        $user->setIsVerified(true);
        $this->entityManager->flush();

        $io->success("Created user with admin privileges email: {$user->getEmail()} password: {$password}");
        return Command::SUCCESS;
    }
}
