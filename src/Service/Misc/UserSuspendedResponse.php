<?php

namespace App\Service\Misc;

use App\Exception\UserIsSuspendedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\Error;
use Twig\Loader\FilesystemLoader;

class UserSuspendedResponse
{
    private int $statusCode = 403;
    private string $message;
    private \DateTime|null $expiresAt;
    private string $reason;

    public function __construct(
        private readonly UserIsSuspendedException $throwable,
        private readonly Environment              $twig,
        private readonly LoggerInterface          $logger,
    )
    {
        $this->expiresAt = $this->throwable->getExpiresAt();
        $this->reason = $this->throwable->getReason();
        $this->message = $this->throwable->getMessage();
    }

    public function getResponse(): Response
    {
        $content = $this->makeRender() ?: $this->message;

        return new Response($content, $this->statusCode);
    }

    private function makeRender(): null|string
    {
        try {
            return $this->twig->render("suspended.html.twig", [
                "reason" => $this->reason,
                "expiresAt" => $this->expiresAt,
            ]);
        } catch (Error $error) {
            $this->logger->error('Failed to render user suspension notification template. Reason: {error}', [
                "error" => $error->getMessage(),
            ]);
        }

        return null;
    }
}