<?php

namespace App\EventSubscribers;

use App\Exception\UserIsSuspendedException;
use App\Service\Misc\UserSuspendedResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;
use Twig\Environment;

final readonly class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(private Environment $twig)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => [
                ['processException', 0]
            ]
        ];
    }

    public function processException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        // TODO: logging and more error handling
        if ($throwable instanceof UserIsSuspendedException) {
            $event->setResponse($this->handleUserSuspendedException($throwable));
        }

        if ($throwable instanceof NotFoundHttpException) {
            $this->handleNotFoundException($throwable, $event);
        }
    }

    public function handleUserSuspendedException(UserIsSuspendedException $throwable): Response
    {
        $response = new UserSuspendedResponse($throwable, $this->twig);

        return $response->getResponse();
    }

    private function handleNotFoundException(Throwable $throwable, ExceptionEvent $event): void
    {
        if ($this->isEntityResolverError($throwable)) {
            $message = 'Resource not found.';

        } else {
            $message = 'Page not found.';
        }

        $event->setThrowable(new NotFoundHttpException($message, null, Response::HTTP_NOT_FOUND));
    }

    private function isEntityResolverError(Throwable $throwable): bool
    {
        $class = $throwable->getTrace()[0]['class'] ?? null;

        return $class !== null && str_contains($class, 'EntityValueResolver');
    }
}