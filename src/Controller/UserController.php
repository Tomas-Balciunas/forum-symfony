<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserEditType;
use App\Form\UserPrivateType;
use App\Service\UserDataProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UserController extends AbstractController
{
    #[Route('/profile/{id}', name: 'user_profile', methods: ['GET'])]
    public function index(#[CurrentUser] User $user, UserDataProvider $provider): Response
    {
        $profilePrivateForm = $this->createForm(UserPrivateType::class, null, [
            'action' => $this->generateUrl('user_profile_set_visibility', ['id' => $user->getId()]),
        ]);

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'provider' => $provider->setUser($user),
            'profilePrivateForm' => $profilePrivateForm->createView(),
        ]);
    }

    #[Route('/profile/{id}/set-visibility', name: 'user_profile_set_visibility', methods: ['POST'])]
    public function private(#[CurrentUser] User $user, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(UserPrivateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setIsPrivate(!$user->isPrivate());
            $manager->flush();
        }

         return $this->redirectToRoute('user_profile', ['id' => $user->getId()]);
    }

    #[Route('/profile/{id}/edit', name: 'user_profile_edit', methods: ['GET', 'POST'])]
    public function edit(#[CurrentUser] User $user, Request $request, EntityManagerInterface $manager): Response
    {
        $editForm = $this->createForm(UserEditType::class, $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $manager->flush();
            return $this->redirectToRoute('user_profile', ['id' => $user->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'editForm' => $editForm->createView(),
        ]);
    }
}
