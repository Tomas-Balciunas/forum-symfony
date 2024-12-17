<?php

namespace App\Helper;

use App\Entity\User;
use App\Entity\Verification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

readonly class VerificationHelper
{
    public function __construct(
        private MailerInterface        $mailer,
        private EntityManagerInterface $manager
    )
    {
    }

    public function generateKey(): string
    {
        return bin2hex(random_bytes(32));
    }

    public function sendVerificationEmail(User $user, string $key): void
    {
        $email = (new Email())->from('test@test.com')
            ->to($user->getEmail())
            ->subject('verification code')
            ->text('verification code is '.$key . '. Code expires in 10 minutes.');
        $this->mailer->send($email);
    }

    public function makeNewVerification(User $user, string $key, \DateTime $expiresAt): void
    {
        $verification = new Verification();
        $verification->setVerificationCode($key);
        $verification->setExpiresAt($expiresAt);
        $verification->setUser($user);
        $this->manager->persist($verification);
    }
}