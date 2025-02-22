<?php

namespace App\Controller;

use App\Data\Roles;
use App\Entity\User;
use App\Form\PermanentSuspendType;
use App\Form\SuspendType;
use App\Repository\UserRepository;
use App\Service\Misc\PermissionAuthorization;
use App\Service\Misc\UserFullDataProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/panel')]
class AuthorityController extends AbstractController
{
    public function __construct(private readonly PermissionAuthorization $authorize) {}

    #[Route('', name: 'admin_panel', defaults: ['page' => 1], methods: ['GET'])]
    #[Route('/page/{page}', name: 'admin_panel_paginated', requirements: ['page' => '\d+'], methods: ['GET'])]
    public function index(UserRepository $repository, int $page, Request $request): Response
    {
        $searchQuery = trim($request->get("search")) ?: null;

        try {
            $this->authorize->role(Roles::ROLE_ADMIN);
            $paginator = $repository->findPaginatedUsers($page, $searchQuery);
            $paginator->paginate();
        } catch (AccessDeniedException $e) {
            //TODO: log
            return $this->redirectToRoute('home');
        }

        return $this->render('admin/index.html.twig', [
            'paginator' => $paginator,
            'path' => 'admin_panel',
            'paginationPath' => 'admin_panel_paginated',
            'searchQuery' => $searchQuery,
        ]);
    }

    #[Route('/manage/{user}', name: 'admin_manage', methods: ['GET'])]
    public function manager(User $user, UserFullDataProvider $provider): Response
    {
        try {
            $this->authorize->role(Roles::ROLE_ADMIN);

            $suspendForm = $this->createForm(SuspendType::class, null, [
                'action' => $this->generateUrl('suspend_user', ['user' => $user->getId()]),
                'method' => 'POST',
            ]);
            $permanentSuspendForm = $this->createForm(PermanentSuspendType::class, null, [
                'action' => $this->generateUrl('suspend_user_permanent', ['user' => $user->getId()]),
                'method' => 'POST',
            ]);
        } catch (AccessDeniedException $e) {
            //TODO: log
            return $this->redirectToRoute('home');
        }

        return $this->render('admin/manager.html.twig', [
            'user' => $user,
            'provider' => $provider->setUser($user),
            'suspendForm' => $suspendForm->createView(),
            'permanentSuspendForm' => $permanentSuspendForm->createView(),
        ]);
    }
}
