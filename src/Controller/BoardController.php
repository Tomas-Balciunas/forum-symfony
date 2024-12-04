<?php

namespace App\Controller;

use App\Data\Permissions;
use App\Entity\Board;
use App\Form\BoardType;
use App\Service\BoardService;
use App\Service\PermissionAuthorization;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
// TODO: categories
#[Route('/board')]
class BoardController extends AbstractController
{
    public function __construct(private readonly PermissionAuthorization $authorization,
                                private readonly BoardService            $service)
    {
    }

    #[Route('/create', name: 'board_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $board = new Board();
        $form = $this->createForm(BoardType::class, $board);

        try {
            $this->authorization->authorize(Permissions::BOARD_CREATE);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->service->handleCreateBoard($board, $form->get('access')->getData());
                    $this->addFlash('success', 'Successfully created board.');
                } catch (Exception $e) {
                    $this->addFlash('error', $e->getMessage());
                }
            }
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('home');
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

    #[Route('/{id}/edit', name: 'board_edit', methods: ['GET', 'POST'])]
    public function edit(Board $board, Request $request): Response
    {
        $form = $this->createForm(BoardType::class, $board);

        try {
            $this->authorization->authorize(Permissions::BOARD_EDIT, $board);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->service->handleUpdateBoard($board, $form->get('access')->getData());

                return $this->redirectToRoute('board_show', ['id' => $board->getId()]);
            }
        } catch (AccessDeniedException $e) {
            $this->addFlash('error', $e->getMessage());
//            return $this->redirectToRoute('board_show', ['id' => $board->getId()]);
        }

        return $this->render('board/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
