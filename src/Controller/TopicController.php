<?php

namespace App\Controller;

use App\Data\Permissions;
use App\Entity\Board;
use App\Entity\Topic;
use App\Entity\User;
use App\Form\PostType;
use App\Form\TopicLockType;
use App\Form\TopicMoveType;
use App\Form\TopicType;
use App\Form\TopicVisibilityType;
use App\Repository\PostRepository;
use App\Service\PermissionAuthorization;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\Service\Attribute\Required;

class TopicController extends AbstractController
{
    public function __construct(private readonly PermissionAuthorization $authorization)
    {
    }

    #[Route('/board/{id}/create-topic', name: 'topic_create', methods: ['GET', 'POST'])]
    public function create(Request $request, Board $board, #[CurrentUser] User $user, EntityManagerInterface $manager): Response
    {
        $topic = new Topic();
        $form = $this->createForm(TopicType::class, $topic);

        try {
            $this->authorization->authorize(Permissions::TOPIC_CREATE, $board);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $topic->setAuthor($user);
                $board->addTopic($topic);
                $manager->flush();

                $this->addFlash('success', 'Topic created.');
                return $this->redirectToRoute('board_show', ['id' => $board->getId()]);
            }
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('board_show', ['id' => $board->getId()]);
        }

        return $this->render('topic/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/topic/{id}', name: 'topic_show', defaults: ['page' => '1'], methods: ['GET'])]
    #[Route('/topic/{id}/page/{page}', name: 'topic_show_paginated', requirements: ['id' => '\d+', 'page' => '\d+'], methods: ['GET'])]
    public function show(Topic $topic, int $page, PostRepository $postRepository): Response
    {
        $lockForm = $this->createForm(TopicLockType::class, null, [
            'action' => $this->generateUrl('topic_lock', ['id' => $topic->getId()]),
            'method' => 'POST',
        ]);
        $visibilityForm = $this->createForm(TopicVisibilityType::class, null, [
            'action' => $this->generateUrl('topic_visibility', ['id' => $topic->getId()]),
            'method' => 'POST',
        ]);
        $form = $this->createForm(PostType::class, null, [
            'action' => $this->generateUrl('post_create', ['id' => $topic->getId()]),
        ]);

        $posts = $postRepository->findPaginatedPosts($page, $topic->getId());
        $posts->paginate();

        $board = $topic->getBoard();

        return $this->render('topic/show.html.twig', [
            'topic' => $topic,
            'paginator' => $posts,
            'board' => $board,
            'form' => $form->createView(),
            'lockForm' => $lockForm->createView(),
            'visibilityForm' => $visibilityForm->createView(),
            'path' => 'topic_show_paginated',
        ]);
    }

    #[Route('/topic/{id}/edit', name: 'topic_edit', methods: ['GET', 'POST'])]
    public function edit(Topic $topic, EntityManagerInterface $manager, Request $request): Response
    {
        $form = $this->createForm(TopicType::class, $topic);

        try {
            $this->authorization->authorize(Permissions::TOPIC_EDIT, $topic);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $manager->flush();
                return $this->redirectToRoute('topic_show', ['id' => $topic->getId()]);
            }
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('topic_show', ['id' => $topic->getId()]);
        }

        return $this->render('topic/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/topic/{id}/lock', name: 'topic_lock', methods: ['POST'])]
    public function lock(Topic $topic, Request $request, EntityManagerInterface $manager): Response
    {
        $lockForm = $this->createForm(TopicLockType::class);
        try {
            $this->authorization->authorize(Permissions::TOPIC_LOCK, $topic);
            $lockForm->handleRequest($request);

            if ($lockForm->isSubmitted() && $lockForm->isValid()) {
                $topic->setIsLocked(!$topic->isLocked());
                $manager->flush();
            }
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('board_show', ['id' => $topic->getId()]);
        }

        return $this->redirectToRoute('topic_show', ['id' => $topic->getId()]);
    }

    #[Route('/topic/{id}/visibility', name: 'topic_visibility', methods: ['POST'])]
    public function visibility(Topic $topic, Request $request, EntityManagerInterface $manager): Response
    {
        $lockForm = $this->createForm(TopicVisibilityType::class);
        $lockForm->handleRequest($request);

        if ($lockForm->isSubmitted() && $lockForm->isValid()) {
            $topic->setIsVisible(!$topic->isVisible());
            $manager->flush();
        }

        return $this->redirectToRoute('topic_show', ['id' => $topic->getId()]);
    }

    #[Route('/topic/{id}/move', name: 'topic_move', methods: ['GET', 'POST'])]
    public function move(Topic $topic, Request $request, EntityManagerInterface $manager): Response
    {
        // TODO: permission. validation?
        $form = $this->createForm(TopicMoveType::class);
        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $board = $form->get('target')->getData();
                $topic->setBoard($board);
                $manager->flush();
                $this->addFlash('success', 'Topic moved.');
            }
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('topic_show', ['id' => $topic->getId()]);
        }

        return $this->render('topic/move.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
