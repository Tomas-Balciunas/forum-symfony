<?php

namespace App\Controller;

use App\Repository\BoardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(BoardRepository $repository): Response
    {
        $boards = $repository->findAllWithCount();
        return $this->render('index/index.html.twig', [
            'boards' => $boards,
        ]);
    }
}
