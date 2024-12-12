<?php

namespace App\Command;

use App\Data\Roles;
use App\Entity\Permission;
use App\Entity\Role;
use App\Helper\PermissionHelper;
use App\Repository\PermissionRepository;
use App\Repository\RoleRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'seed:permissions',
    description: 'Create and/or update roles and permissions.',
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

        foreach ($this->roles->getRolesAndPermissions() as $r => $permissions) {
            $role = $this->roleRepository->findByName($r);

            if (!$role) {
                $role = $this->newRole($r);
                $this->manager->flush();

                $io->writeln('Role ' . $r . ' created.');
            }

            foreach ($permissions as $p) {
                $permission = $this->permissionRepository->findPermissionByName($p);

                if (!$permission) {
                    $permission = $this->newPermission($p);
                }

                if (!$this->hasPermission($role->getPermissions(), $permission)) {
                    $role->setPermission($permission);
                }

            }

            $io->writeln('Permissions for role ' . $r . ' updated.');
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
        $permission->setFormattedName(PermissionHelper::formatName($permission->getName()));
        $this->manager->persist($permission);

        return $permission;
    }

    private function hasPermission(Collection $rolePermissions, Permission $permission): bool
    {
        foreach ($rolePermissions as $rolePermission) {
            if ($rolePermission === $permission) {
                return true;
            }
        }

        return false;
    }
}
