<?php

namespace App\EventSubscriber;

use App\Entity\UserSettings;
use App\Entity\Verification;
use App\Event\UserCreatedEvent;
use App\Event\UserRegisteredEvent;
use App\Event\UserSuspendedEvent;
use App\Event\UserVerifiedEvent;
use App\Helper\UserHelper;
use App\Helper\VerificationHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class UserSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserHelper            $userHelper,
        private VerificationHelper      $verificationHelper,
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserCreatedEvent::NAME => [
                ['applySettings'],
                ['applyUserRole']
            ],
            UserRegisteredEvent::NAME => [
                ['sendVerificationCode']
            ],
            UserVerifiedEvent::NAME => [
                ['applyUserPermissions']

            ],
            UserSuspendedEvent::NAME => [
                ['setUserStatus']
            ],
        ];
    }

    public function applySettings(UserCreatedEvent $event): void
    {
        $user = $event->getUser();
        $this->userHelper->setDefaultSettings($user);
    }

    public function applyUserRole(UserCreatedEvent $event): void
    {
        $user = $event->getUser();
        $this->userHelper->setDefaultUserRole($user);
    }

    public function sendVerificationCode(UserRegisteredEvent $event): void
    {
        $user = $event->getUser();
        $key = $this->verificationHelper->generateKey();
        $expiresAt = (new \DateTime())->add(new \DateInterval('PT10M'));

        $this->verificationHelper->sendVerificationEmail($user, $key);
        $this->verificationHelper->makeNewVerification($user, $key, $expiresAt);
    }

    public function applyUserPermissions(UserVerifiedEvent $event): void
    {
        $user = $event->getVerification()->getUser();
        $this->userHelper->grantDefaultUserPermissions($user);
    }

    public function setUserStatus(UserSuspendedEvent $event): void
    {
        $user = $event->getUser();
        $this->userHelper->setUserStatus($user, 'suspended');
    }
}