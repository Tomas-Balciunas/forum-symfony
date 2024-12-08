<?php

namespace App\Controller;

use App\Data\Messages;
use App\Data\Permissions;
use App\Entity\User;
use App\Form\AccountSettingsType;
use App\Form\UserEditType;
use App\Form\UserPrivateType;
use App\Helper\PostHelper;
use App\Repository\PostRepository;
use App\Repository\TopicRepository;
use App\Service\PermissionAuthorization;
use App\Service\UserDataProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UserController extends AbstractController
{
    public function __construct(private PermissionAuthorization $authorization)
    {
    }

    #[Route('/user/{id}', name: 'user_profile_public', methods: ['GET'])]
    public function profile(User $user, UserDataProvider $provider, PostHelper $helper): Response
    {
        try {
            $this->authorization->authorize(Permissions::USER_VIEW_PROFILE, $user);
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', Messages::PERMISSION_DENIED[Permissions::USER_VIEW_PROFILE]);
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
            $this->addFlash('success', 'Your settings have been saved.');
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
        $topics = $repository->findPaginatedUserTopics($page, $user);
        $topics->paginate();

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
        $topics = $repository->findPaginatedUserPosts($page, $user);
        $topics->paginate();

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
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setIsPrivate(!$user->isPrivate());
            $manager->flush();
        }

         return $this->redirectToRoute('user_account');
    }

    #[Route('/profile/edit', name: 'user_profile_edit', methods: ['GET', 'POST'])]
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
