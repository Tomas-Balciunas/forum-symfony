<?php

namespace App\Controller;

use App\Data\Permissions;
use App\Entity\User;
use App\Form\AccountSettingsType;
use App\Form\UserEditType;
use App\Form\UserPrivateType;
use App\Helper\PostHelper;
use App\Repository\PostRepository;
use App\Repository\TopicRepository;
use App\Service\Misc\AddFlashMessages;
use App\Service\Misc\PermissionAuthorization;
use App\Service\Misc\UserDataProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UserController extends AbstractController
{
    public function __construct(
        private readonly PermissionAuthorization $authorize,
        private readonly AddFlashMessages        $flashMessages
    ) {}

    #[Route('/user/{id}', name: 'user_profile_public', methods: ['GET'])]
    public function profile(User $user, UserDataProvider $provider, PostHelper $helper): Response
    {
        try {
            $this->authorize->permission(Permissions::USER_VIEW_PROFILE, $user);
        } catch (AccessDeniedException $e) {
            $this->flashMessages->addErrorMessage($e->getMessage());

            return $this->redirectToRoute('home');
        }

        return $this->render('user/public_profile.html.twig', [
            'user' => $user,
            'provider' => $provider->setUser($user),
            'helper' => $helper,
        ]);
    }

    #[Route('/account', name: 'user_account', methods: ['GET', 'POST'])]
    public function index(#[CurrentUser] User $user, UserDataProvider $provider, Request $request, EntityManagerInterface $manager): Response
    {
        $profilePrivateForm = $this->createForm(UserPrivateType::class, null, [
            'action' => $this->generateUrl('user_profile_set_visibility', ['id' => $user->getId()]),
        ]);

        $settingsForm = $this->createForm(AccountSettingsType::class, $user->getSettings());
        $settingsForm->handleRequest($request);

        if ($settingsForm->isSubmitted() && $settingsForm->isValid()) {
            $manager->flush();
            $this->flashMessages->addSuccessMessage('Your settings have been saved.');
        }

        return $this->render('user/account.html.twig', [
            'user' => $user,
            'provider' => $provider->setUser($user),
            'profilePrivateForm' => $profilePrivateForm->createView(),
            'settingsForm' => $settingsForm->createView(),
        ]);
    }

    #[Route('/user/{id}/topics', name: 'user_topics', defaults: ['page' => 1], methods: ['GET'])]
    #[Route('/user/{id}/topics/page/{page}', name: 'user_topics_paginated', requirements: ['id' => '\d+', 'page' => '\d+'], methods: ['GET'])]
    public function userTopics(User $user, int $page, TopicRepository $repository): Response
    {
        try {
            $this->authorize->permission(Permissions::MISC_VIEW_USER_TOPICS, $user);
            $topics = $repository->findPaginatedUserTopics($page, $user);
            $topics->paginate();
        } catch (AccessDeniedException $e) {
            $this->flashMessages->addErrorMessage($e->getMessage());

            return $this->redirectToRoute('user_profile_public', ['id' => $user->getId()]);
        }

        return $this->render('user/topics.html.twig', [
            'user' => $user,
            'paginator' => $topics,
            'path' => 'user_topics',
            'paginationPath' => 'user_topics_paginated'
        ]);
    }

    #[Route('/user/{id}/posts', name: 'user_posts', defaults: ['page' => 1], methods: ['GET'])]
    #[Route('/user/{id}/posts/page/{page}', name: 'user_posts_paginated', requirements: ['id' => '\d+', 'page' => '\d+'], methods: ['GET'])]
    public function userPosts(User $user, int $page, PostRepository $repository, PostHelper $helper): Response
    {
        try {
            $this->authorize->permission(Permissions::MISC_VIEW_USER_POSTS, $user);
            $topics = $repository->findPaginatedUserPosts($page, $user);
            $topics->paginate();
        } catch (AccessDeniedException $e) {
            $this->flashMessages->addErrorMessage($e->getMessage());

            return $this->redirectToRoute('user_profile_public', ['id' => $user->getId()]);
        }

        return $this->render('user/posts.html.twig', [
            'user' => $user,
            'paginator' => $topics,
            'helper' => $helper,
            'path' => 'user_posts',
            'paginationPath' => 'user_posts_paginated'
        ]);
    }

    #[Route('/profile/set-visibility', name: 'user_profile_set_visibility', methods: ['POST'])]
    public function private(#[CurrentUser] User $user, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(UserPrivateType::class);

        try {
            if ($user->isPrivate()) {
                $this->authorize->permission(Permissions::USER_SET_PUBLIC);
            } else {
                $this->authorize->permission(Permissions::USER_SET_PRIVATE);
            }

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user->setIsPrivate(!$user->isPrivate());
                $manager->flush();
            }
        } catch (AccessDeniedException $e) {
            $this->flashMessages->addErrorMessage($e->getMessage());
        } finally {
            return $this->redirectToRoute('user_account');
        }
    }

    #[Route('/account/edit', name: 'user_profile_edit', methods: ['GET', 'POST'])]
    public function edit(#[CurrentUser] User $user, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $editForm = $this->createForm(UserEditType::class, $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $plainPassword = $editForm->get('plainPassword')->getData() ?: '';
            $currentPassword = $editForm->get('currentPassword')->getData() ?: '';

            if (!empty($plainPassword) && !$userPasswordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('error', 'Invalid current password.');

                return $this->redirectToRoute('user_account');
            } else {
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
                $this->addFlash('success', 'Your password has been updated.');
            }

            $manager->flush();
            $this->addFlash('success', 'Your changes have been saved.');

            return $this->redirectToRoute('user_account');
        }

        return $this->render('user/edit.html.twig', [
            'editForm' => $editForm->createView(),
        ]);
    }
}
