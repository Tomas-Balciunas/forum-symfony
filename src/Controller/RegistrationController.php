<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Verification;
use App\Event\UserRegisteredEvent;
use App\Event\UserVerifiedEvent;
use App\Form\RegistrationFormType;
use App\Repository\VerificationRepository;
use App\Service\Misc\AddFlashMessages;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly AddFlashMessages $addFlashMessages,
        private readonly EventDispatcherInterface $dispatcher
    )
    {
    }

    // TODO: no access when logged in
    // TODO: resend activation code
    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new UserRegisteredEvent($user);
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $this->dispatcher->dispatch($event, UserRegisteredEvent::NAME);
            $entityManager->flush();
            $this->addFlashMessages->addSuccessMessage('Registration successful.');
            $this->addFlashMessages->addSuccessMessage('A verification email has been sent to your registered email address.');

            return $this->redirectToRoute('login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('verify', name: 'verify')]
    public function verifyAccount(
        Request $request,
        VerificationRepository $repository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $code = htmlspecialchars($request->get('code'));

        if (empty($code)) {

            $this->addFlashMessages->addErrorMessage('Verification code is missing.');
            return $this->redirectToRoute('home');
        }

        $verification = $repository->findOneBy(['verification_code' => $code]);

        if (null === $verification) {
            $this->addFlashMessages->addErrorMessage('Verification code is invalid.');
            return $this->redirectToRoute('home');
        }

        if ($verification->getStatus() === 'verified') {
            return $this->redirectToRoute('home');
        }

        if ($verification->getExpiresAt() < new \DateTime('now')) {
            $this->addFlashMessages->addErrorMessage('Verification code has expired.');
            return $this->redirectToRoute('home');
        }

        $event = new UserVerifiedEvent($verification);
        $user = $verification->getUser();
        $user->setIsVerified(true);
        $verification->setStatus(Verification::STATUS_VERIFIED);

        $this->dispatcher->dispatch($event, UserVerifiedEvent::NAME);
        $entityManager->flush();

        $this->addFlashMessages->addSuccessMessage('Your account has been verified. You may now perform actions.');

        return $this->redirectToRoute('home');
    }
}
