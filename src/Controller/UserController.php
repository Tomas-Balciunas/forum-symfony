<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserEditType;
use App\Form\UserPrivateType;
use App\Repository\PostRepository;
use App\Repository\TopicRepository;
use App\Service\UserDataProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UserController extends AbstractController
{
    #[Route('/user/{id}', name: 'user_profile_public', methods: ['GET'])]
    public function profile(User $user, Request $request, UserDataProvider $provider): Response
    {
        dump($provider->setUser($user)->getLatestPosts());
        return $this->render('user/public_profile.html.twig', [
            'user' => $user,
            'provider' => $provider->setUser($user),
        ]);
    }

    #[Route('/profile', name: 'user_profile', methods: ['GET'])]
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
    public function userPosts(User $user, int $page, PostRepository $repository): Response
    {
        $topics = $repository->findPaginatedUserPosts($page, $user);
        $topics->paginate();

        return $this->render('user/posts.html.twig', [
            'user' => $user,
            'paginator' => $topics,
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

         return $this->redirectToRoute('user_profile', ['id' => $user->getId()]);
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
