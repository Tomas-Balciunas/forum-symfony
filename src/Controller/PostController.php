<?php

namespace App\Controller;

use App\Data\Permissions;
use App\Entity\Post;
use App\Entity\Topic;
use App\Entity\User;
use App\Exception\Post\CreatePostException;
use App\Form\PostType;
use App\Service\Misc\AddFlashMessages;
use App\Service\PermissionAuthorization;
use App\Service\PostService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class PostController extends AbstractController
{
    // TODO: add delete post

    public function __construct(
        private readonly PermissionAuthorization $authorize,
        private readonly PostService             $service,
        private readonly AddFlashMessages        $flashMessages
    ) {}

    #[Route('/topic/{id}/new-post', name: 'post_create', methods: ['POST'])]
    public function create(Topic $topic, #[CurrentUser] User $user, Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);

        try {
            $this->authorize->permission(Permissions::POST_CREATE);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->service->handleCreatePost($post, $topic, $user);
            }

        } catch (CreatePostException $e) {
            $this->flashMessages->addErrorMessages($e->getExceptionErrors());
        } catch (AccessDeniedException $e) {
            $this->flashMessages->addErrorMessage($e->getMessage());
        } finally {
            return $this->redirectToRoute('topic_show', ['id' => $topic->getId()]);
        }
    }

    #[Route('/post/{id}/edit', name: 'post_edit', methods: ['GET', 'POST'])]
    public function edit(Post $post, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(PostType::class, $post);

        try {
            $this->authorize->permission(Permissions::POST_EDIT, $post);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $manager->flush();
            }
        } catch (AccessDeniedException $e) {
            $this->flashMessages->addErrorMessage($e->getMessage());
            return $this->redirectToRoute('topic_show', ['id' => $post->getTopic()->getId()]);
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
