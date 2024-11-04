<?php

namespace App\Controller;

use App\Repository\TopicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TopicController extends AbstractController
{
    #[Route('/topics', name: 'topics')]
    public function index(TopicRepository $repository): Response
    {
        return $this->render('topic/index.html.twig', [
            'controller_name' => 'TopicController',
            'topic' => $repository->findAll(),
        ]);
    }
}
