<?php

namespace App\Command;

use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[AsCommand(
    name: 'user:promote',
    description: 'Give user admin rights.',
)]
class PromoteAdminCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository         $userRepository,
        private readonly RoleRepository         $roleRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('username', InputArgument::REQUIRED, 'Username');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $role = $this->roleRepository->findOneBy(['name' => 'ROLE_ADMIN']);
        $username = $input->getArgument('username');

        $user = $this->userRepository->findOneBy(['username' => $username]);

        if (!$user) {
            $io->error("User $username not found.");

            return Command::FAILURE;
        }

        $user->setRole($role);
        $user->setPermissions($role->getPermissions());
        $this->entityManager->flush();

        $io->success("User {$user->getUsername()} has been granted admin privileges. (relog to see changes)");

        return Command::SUCCESS;
    }
}
