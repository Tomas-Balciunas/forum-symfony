<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

final readonly class ExceptionSubscriber implements EventSubscriberInterface
{
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

        if ($throwable instanceof NotFoundHttpException) {
            $this->handleNotFoundException($throwable, $event);
        }
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