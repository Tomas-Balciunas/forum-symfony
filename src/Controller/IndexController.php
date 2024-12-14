<?php

namespace App\Controller;

use App\Repository\BoardRepository;
use App\Repository\PostRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        PostRepository $postRepository,
        UserRepository $userRepository,
        TopicRepository $topicRepository
    ): Response
    {
        $latestPosts = $postRepository->findLatestPosts();
        $latestUsers = $userRepository->findLatestUsers();
        $latestTopics = $topicRepository->findLatestTopics();
        $mostActiveUsers = $userRepository->findHighestPostCount();
        $mostActiveTopics = $topicRepository->findHighestPostCount();

        return $this->render('index/index.html.twig', [
            'latestPosts' => $latestPosts,
            'latestUsers' => $latestUsers,
            'latestTopics' => $latestTopics,
            'mostActiveUsers' => $mostActiveUsers,
            'mostActiveTopics' => $mostActiveTopics,
        ]);
    }

    #[Route('/forum', name: 'forum')]
    public function forum(BoardRepository $repository): Response
    {
        $boards = $repository->findAllWithCount();

        return $this->render('index/forum.html.twig', [
        'boards' => $boards,
    ]);
    }
}
