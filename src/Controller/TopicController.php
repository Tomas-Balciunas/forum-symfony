<?php

namespace App\Controller;

use App\Data\Permissions;
use App\Entity\Board;
use App\Entity\DTO\TopicDTO;
use App\Entity\Topic;
use App\Entity\User;
use App\Event\TopicPrepareEvent;
use App\Form\PostType;
use App\Form\TopicImportantType;
use App\Form\TopicLockType;
use App\Form\TopicMoveType;
use App\Form\TopicType;
use App\Form\TopicVisibilityType;
use App\Repository\PostRepository;
use App\Service\Misc\AddFlashMessages;
use App\Service\Misc\PermissionAuthorization;
use App\Service\TopicService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route('/forum')]
class TopicController extends AbstractController
{
    public function __construct(
        private readonly PermissionAuthorization $authorize,
        private readonly TopicService            $service,
        private readonly AddFlashMessages        $flashMessages
    ) {}

    #[Route('/board/{id}/create-topic', name: 'topic_create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        Board $board,
        #[CurrentUser] User $user,
        EventDispatcherInterface $dispatcher
    ): Response
    {
        $topicDto = new TopicDTO();
        $form = $this->createForm(TopicType::class, $topicDto);

        try {
            $this->authorize->permission(Permissions::TOPIC_CREATE, $board);
            $this->authorize->role($board->getAccess()->getName());
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new TopicPrepareEvent($user);
                $dispatcher->dispatch($event, TopicPrepareEvent::NAME);

                $this->service->handleCreateTopic($topicDto, $board, $user);
                $this->flashMessages->addSuccessMessage('Topic created.');

                return $this->redirectToRoute('board_show', ['id' => $board->getId()]);
            }
        } catch (AccessDeniedException $e) {
            $this->flashMessages->addErrorMessage($e->getMessage());

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
        $topicDto = TopicDTO::hydrate($topic);

        $lockForm = $this->createForm(TopicLockType::class, null, [
            'action' => $this->generateUrl('topic_lock', ['id' => $topicDto->id]),
            'method' => 'POST',
        ]);
        $visibilityForm = $this->createForm(TopicVisibilityType::class, null, [
            'action' => $this->generateUrl('topic_visibility', ['id' => $topicDto->id]),
            'method' => 'POST',
        ]);
        $importantForm = $this->createForm(TopicImportantType::class, null, [
            'action' => $this->generateUrl('topic_important', ['id' => $topicDto->id]),
            'method' => 'POST',
        ]);
        $form = $this->createForm(PostType::class, null, [
            'action' => $this->generateUrl('post_create', ['id' => $topicDto->id, 'page' => $page]),
        ]);

        $posts = $postRepository->findPaginatedPosts($page, $topicDto->id);
        $posts->paginate();

        $board = $topicDto->board;

        return $this->render('topic/show.html.twig', [
            'topic' => $topicDto,
            'paginator' => $posts,
            'board' => $board,
            'form' => $form->createView(),
            'lockForm' => $lockForm->createView(),
            'visibilityForm' => $visibilityForm->createView(),
            'importantForm' => $importantForm->createView(),
            'path' => 'topic_show_paginated',
        ]);
    }

    #[Route('/topic/{id}/edit', name: 'topic_edit', methods: ['GET', 'POST'])]
    public function edit(Topic $topic, EntityManagerInterface $manager, Request $request): Response
    {
        $topicDto = TopicDTO::hydrate($topic);
        $form = $this->createForm(TopicType::class, $topicDto);

        try {
            $this->authorize->permission(Permissions::TOPIC_EDIT, $topic);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $topic->setBody($topicDto->body);
                $topic->setTitle($topicDto->title);
                $manager->flush();
                $this->flashMessages->addSuccessMessage('Topic updated.');

                return $this->redirectToRoute('topic_show', ['id' => $topic->getId()]);
            }
        } catch (AccessDeniedException $e) {
            $this->flashMessages->addErrorMessage($e->getMessage());

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
            $this->authorize->permission(Permissions::TOPIC_LOCK, $topic);
            $lockForm->handleRequest($request);

            if ($lockForm->isSubmitted() && $lockForm->isValid()) {
                $topic->setIsLocked(!$topic->getIsLocked());
                $manager->flush();
            }
        } catch (AccessDeniedException $e) {
            $this->flashMessages->addErrorMessage($e->getMessage());
        } finally {
            return $this->redirectToRoute('topic_show', ['id' => $topic->getId()]);
        }
    }

    #[Route('/topic/{id}/visibility', name: 'topic_visibility', methods: ['POST'])]
    public function visibility(Topic $topic, Request $request, EntityManagerInterface $manager): Response
    {
        try {
            if ($topic->getIsVisible()) {
                $this->authorize->permission(Permissions::TOPIC_SET_HIDDEN, $topic);
            } else {
                $this->authorize->permission(Permissions::TOPIC_SET_VISIBLE, $topic);
            }

            $lockForm = $this->createForm(TopicVisibilityType::class);
            $lockForm->handleRequest($request);

            if ($lockForm->isSubmitted() && $lockForm->isValid()) {
                $topic->setIsVisible(!$topic->getIsVisible());
                $manager->flush();
            }
        } catch (AccessDeniedException $e) {
            $this->flashMessages->addErrorMessage($e->getMessage());
        } finally {
            return $this->redirectToRoute('topic_show', ['id' => $topic->getId()]);
        }
    }

    #[Route('/topic/{id}/important', name: 'topic_important', methods: ['POST'])]
    public function important(Topic $topic, Request $request, EntityManagerInterface $manager): Response
    {
        try {
            $this->authorize->permission(Permissions::TOPIC_SET_IMPORTANT, $topic);

            $lockForm = $this->createForm(TopicImportantType::class);
            $lockForm->handleRequest($request);

            if ($lockForm->isSubmitted() && $lockForm->isValid()) {
                $topic->setIsImportant(!$topic->getIsImportant());
                $manager->flush();
            }
        } catch (AccessDeniedException $e) {
            $this->flashMessages->addErrorMessage($e->getMessage());
        } finally {
            return $this->redirectToRoute('topic_show', ['id' => $topic->getId()]);
        }
    }

    #[Route('/topic/{id}/move', name: 'topic_move', methods: ['GET', 'POST'])]
    public function move(Topic $topic, Request $request): Response
    {
        $form = $this->createForm(TopicMoveType::class);

        try {
            $this->authorize->permission(Permissions::TOPIC_MOVE, $topic);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $board = $form->get('target')->getData();
                $this->service->handleMoveTopic($topic, $board);
                $this->flashMessages->addSuccessMessage('Topic moved.');
            }
        } catch (AccessDeniedException $e) {
            $this->flashMessages->addErrorMessage($e->getMessage());

            return $this->redirectToRoute('topic_show', ['id' => $topic->getId()]);
        }

        return $this->render('topic/move.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
