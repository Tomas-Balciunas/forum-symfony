<?php

namespace App\Controller;

use App\Data\Permissions;
use App\Entity\Board;
use App\Form\BoardType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/board')]
class BoardController extends AbstractController
{
    #[Route('/create', name: 'board_create', methods: ['GET', 'POST'])]
    #[IsGranted(Permissions::BOARD_CREATE)]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $board = new Board();
        $form = $this->createForm(BoardType::class, $board);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $manager->persist($board);
                $manager->flush();
                $this->addFlash('success', 'Successfully created board.');
            } catch (Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('board/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'board_show', methods: ['GET'])]
    public function show(Board $board): Response
    {
        $topics = $board->getTopics();

        return $this->render('board/show.html.twig', [
            'controller_name' => 'GroupController',
            'board' => $board,
            'topics' => $topics,
        ]);
    }
}
