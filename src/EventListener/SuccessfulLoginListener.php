<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

#[AsEventListener(event: 'security.authentication.success')]
readonly class SuccessfulLoginListener
{
    public function __construct(private LoggerInterface $logger)
    {

    }

    public function __invoke(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        $this->logger->info('Successful login: ' . $user->getUserIdentifier());
    }
}