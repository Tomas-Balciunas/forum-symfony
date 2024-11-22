<?php

namespace App\EventListener;

use App\Data\Roles;
use App\Event\UserCreatedEvent;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class UserCreatedSubscriber implements EventSubscriberInterface
{
    private const DEFAULT_ROLE = 'ROLE_USER';

    public function __construct(
        private EntityManagerInterface $manager,
        private RoleRepository         $roleRepository,
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserCreatedEvent::NAME => [
                'applyUserRoles'
            ]
        ];
    }

    public function applyUserRoles(UserCreatedEvent $event): void
    {
        $user = $event->getUser();
        $role = $this->roleRepository->findOneBy(['name' => self::DEFAULT_ROLE]);
        $permissions = $role->getPermissions();
        $user->setRole($role);
        $user->setPermissions($permissions);
        $this->manager->flush();
    }
}