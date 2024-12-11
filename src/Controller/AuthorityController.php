<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PermanentSuspendType;
use App\Form\SuspendType;
use App\Repository\UserRepository;
use App\Service\UserFullDataProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/panel')]
class AuthorityController extends AbstractController
{
    // TODO: authorization and user search
    #[Route('', name: 'admin_panel')]
    public function index(UserRepository $repository, EntityManagerInterface $manager): Response
    {
        $users = $repository->findAll();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $users,
        ]);
    }

    //TODO: authorization
    #[Route('/manage/{user}', name: 'admin_manage', methods: ['GET'])]
    public function manager(User $user, UserFullDataProvider $provider): Response
    {
        $suspendForm = $this->createForm(SuspendType::class, null, [
            'action' => $this->generateUrl('suspend_user', ['user' => $user->getId()]),
            'method' => 'POST',
        ]);
        $permanentSuspendForm = $this->createForm(PermanentSuspendType::class, null, [
            'action' => $this->generateUrl('suspend_user_permanent', ['user' => $user->getId()]),
            'method' => 'POST',
        ]);

        return $this->render('admin/manager.html.twig', [
            'user' => $user,
            'provider' => $provider->setUser($user),
            'suspendForm' => $suspendForm->createView(),
            'permanentSuspendForm' => $permanentSuspendForm->createView(),
        ]);
    }
}
