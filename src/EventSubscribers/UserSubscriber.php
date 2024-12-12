<?php

namespace App\EventSubscribers;

use App\Entity\UserSettings;
use App\Event\PostPrepareEvent;
use App\Event\UserCreatedEvent;
use App\Event\UserSuspendedEvent;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final readonly class UserSubscriber implements EventSubscriberInterface
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
                ['applyUserRoles'],
                ['applySettings']
            ],
            UserSuspendedEvent::NAME => [
                ['setUserStatus']
            ],
        ];
    }

    public function applyUserRoles(UserCreatedEvent $event): void
    {
        $user = $event->getUser();
        $role = $this->roleRepository->findOneBy(['name' => self::DEFAULT_ROLE]);
        $user->setRole($role);
        $permissions = $role->getPermissions();
        $user->setPermissions($permissions);
        $this->manager->flush();
    }

    public function applySettings(UserCreatedEvent $event): void
    {
        $user = $event->getUser();
        $settings = new UserSettings();
        $settings->setUser($user);
        $this->manager->persist($settings);
        $this->manager->flush();
    }

    public function setUserStatus(UserSuspendedEvent $event): void
    {
        $user = $event->getUser();

        $user->setStatus('suspended');
        $this->manager->flush();
    }


}