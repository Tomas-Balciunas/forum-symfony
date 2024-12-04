<?php

namespace App\Controller;

use App\Data\Permissions;
use App\Entity\Post;
use App\Entity\Topic;
use App\Entity\User;
use App\Form\PostType;
use App\Service\PermissionAuthorization;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class PostController extends AbstractController
{
    public function __construct(private readonly PermissionAuthorization $authorization)
    {
    }

    #[Route('/topic/{id}/post', name: 'post_create', methods: ['POST'])]
    public function create(Topic $topic, #[CurrentUser] User $user, Request $request, EntityManagerInterface $manager): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);

        try {
            $this->authorization->authorize(Permissions::POST_CREATE, $topic);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $post->setAuthor($user);
                $topic->addPost($post);
                $manager->flush();
            }
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('topic_show', ['id' => $topic->getId()]);
        }


        return $this->redirectToRoute('topic_show', ['id' => $topic->getId()]);
    }

    #[Route('/post/{id}/edit', name: 'post_edit', methods: ['GET', 'POST'])]
    public function edit(Post $post, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(PostType::class, $post);

        try {
            $this->authorization->authorize(Permissions::POST_EDIT, $post);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $manager->flush();
            }
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('topic_show', ['id' => $post->getTopic()->getId()]);
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
