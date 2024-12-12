<?php

namespace App\Controller;

use App\Data\Permissions;
use App\Entity\DTO\PostDTO;
use App\Entity\Post;
use App\Entity\Topic;
use App\Entity\User;
use App\Event\PostPrepareEvent;
use App\Exception\Post\CreatePostException;
use App\Form\PostType;
use App\Helper\PostHelper;
use App\Service\Misc\AddFlashMessages;
use App\Service\Misc\PermissionAuthorization;
use App\Service\PostService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class PostController extends AbstractController
{
    // TODO: add delete post

    public function __construct(
        private readonly PermissionAuthorization $authorize,
        private readonly PostService             $service,
        private readonly AddFlashMessages        $flashMessages,
    )
    {
    }

    #[Route('/topic/{id}/new-post', name: 'post_create', methods: ['POST'])]
    public function create(
        Topic                    $topic,
        #[CurrentUser] User      $user,
        Request                  $request,
        EventDispatcherInterface $dispatcher,
        PostHelper               $helper
    ): Response
    {
        $page = is_numeric($request->query->get('page')) ? (int)$request->query->get('page') : 1;
        $post = new PostDTO();
        $event = new PostPrepareEvent($user);
        $form = $this->createForm(PostType::class, $post);

        try {
            $this->authorize->permission(Permissions::POST_CREATE);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $dispatcher->dispatch($event, PostPrepareEvent::NAME);
                $createdPost = $this->service->handleCreatePost($post, $topic, $user);

                return $this->redirectToRoute('topic_show_paginated', [
                    'id' => $topic->getId(),
                    'page' => $helper->getPostPosition($createdPost->getId(), $topic->getId()),
                    '_fragment' => $createdPost->getId()
                ]);
            }

        } catch (CreatePostException $e) {
            $this->flashMessages->addErrorMessages($e->getExceptionErrors());
        } catch (AccessDeniedException $e) {
            $this->flashMessages->addErrorMessage($e->getMessage());
        }

        return $this->redirectToRoute('topic_show_paginated', ['id' => $topic->getId(), 'page' => $page]);
    }

    #[Route('/post/{id}/edit', name: 'post_edit', methods: ['GET', 'POST'])]
    public function edit(Post $post, Request $request, PostHelper $helper): Response
    {
        $page = is_numeric($request->query->get('page')) ? (int)$request->query->get('page') : 1;

        $postDto = PostDTO::hydrate($post);
        $form = $this->createForm(PostType::class, $postDto);

        try {
            $this->authorize->permission(Permissions::POST_EDIT, $post);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $updatedPost = $this->service->handleUpdatePost($postDto, $post);
                $this->flashMessages->addSuccessMessage('Post has been updated.');

                return $this->redirectToRoute('topic_show_paginated', [
                    'id' => $post->getTopic()->getId(),
                    'page' => $helper->getPostPosition($updatedPost->getId(), $post->getTopic()->getId()),
                    '_fragment' => $updatedPost->getId()
                ]);
            }
        } catch (AccessDeniedException $e) {
            $this->flashMessages->addErrorMessage($e->getMessage());

            return $this->redirectToRoute('topic_show_paginated', ['id' => $post->getTopic()->getId(), 'page' => $page]);
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
