<?php

namespace App\Controller;

use App\Entity\Board;
use App\Entity\Topic;
use App\Entity\User;
use App\Form\PostType;
use App\Form\TopicType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TopicController extends AbstractController
{
    #[Route('/board/{id}/create-topic', name: 'topic_create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, Board $board, #[CurrentUser] User $user, EntityManagerInterface $manager): Response
    {
        $topic = new Topic();
        $form = $this->createForm(TopicType::class, $topic);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $topic->setAuthor($user);
            $board->addTopic($topic);
            $manager->flush();

            $this->addFlash('success', 'Topic created.');
            return $this->redirectToRoute('board_show', ['id' => $board->getId()]);
        }

        return $this->render('topic/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/topic/{id}', name: 'topic_show')]
    public function show(Topic $topic): Response
    {
        $form = $this->createForm(PostType::class, null, [
            'action' => $this->generateUrl('post_create', ['id' => $topic->getId()]),
        ]);
        $posts = $topic->getPosts();
        $board = $topic->getBoard();

        return $this->render('topic/show.html.twig', [
            'topic' => $topic,
            'posts' => $posts,
            'board' => $board,
            'form' => $form,
        ]);
    }

    #[Route('/topic/{id}/edit', name: 'topic_edit', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHOR', subject: 'topic')]
    public function edit(Topic $topic, EntityManagerInterface $manager, Request $request): Response
    {
        $form = $this->createForm(TopicType::class, $topic);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
        }

        return $this->render('topic/edit.html.twig', [
           'form' => $form->createView(),
        ]);
    }
}
