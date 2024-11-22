<?php

namespace App\Command;

use App\Data\Roles;
use App\Entity\Permission;
use App\Entity\Role;
use App\Repository\PermissionRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'seed:roles',
    description: 'Create a seed for all roles.',
)]
class SeedRolesCommand extends Command
{
    public function __construct(
        private readonly RoleRepository         $roleRepository,
        private readonly PermissionRepository   $permissionRepository,
        private readonly EntityManagerInterface $manager,
        private readonly Roles $roles
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

        $dbRoles = $this->roleRepository->findAll();
        $dbPermissions = $this->permissionRepository->findAll();

        $permissionsMap = [];
        $rolesMap = [];

        foreach ($dbRoles as $role) {
            $rolesMap[$role->getName()] = $role;
        }

        foreach ($dbPermissions as $permission) {
            $permissionsMap[$permission->getName()] = $permission;
        }

        foreach ($this->roles->getRolesAndPermissions() as $r => $permissions) {
            $role = $rolesMap[$r] ?? null;

            if (!$role) {
                $role = $this->newRole($r);

                $io->writeln('Role ' . $r . ' created.');
            }

            foreach ($permissions as $p) {
                $permission = $permissionsMap[$p] ?? null;

                if (!$permission) {
                    $permission = $this->newPermission($p);
                    $permissionsMap[$permission->getName()] = $permission;
                }

                $role->setPermission($permission);
            }

            $io->writeln('Permissions for role ' . $r . ' created.');
            $this->manager->flush();
        }

        return Command::SUCCESS;
    }

    private function newRole(string $name): Role
    {
        $role = new Role();
        $role->setName($name);
        $this->manager->persist($role);

        return $role;
    }

    private function newPermission(string $name): Permission
    {
        $permission = new Permission();
        $permission->setName($name);
        $this->manager->persist($permission);

        return $permission;
    }
}
