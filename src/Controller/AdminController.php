<?php

namespace App\Controller;

use App\Entity\Permission;
use App\Entity\User;
use App\Form\PermissionRestrictType;
use App\Service\AdminService;
use App\Service\UserDataProvider;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/manage/{user}', name: 'admin_manage', methods: ['GET'])]
    public function manager(User $user, ManagerRegistry $registry): Response
    {
        $provider = new UserDataProvider($user, $registry);

        return $this->render('admin/manager.html.twig', [
            'user' => $user,
            'provider' => $provider,
        ]);
    }

    #[Route('/manage/{user}/permit/{permission}', name: 'admin_grant_permission')]
    public function grantPermission(User $user, Permission $permission, AdminService $service, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('permit_user_' . $user->getId() . '_' . $permission->getId(), $request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');
            throw new AccessDeniedHttpException();
        }

        $service->handleGrantPermission($user, $permission);

        return $this->redirectToRoute('admin_manage', ['user' => $user->getId()]);
    }

    #[Route('/manage/{user}/revoke/{permission}', name: 'admin_revoke_permission')]
    public function revokePermission(User $user, Permission $permission, AdminService $service, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('revoke_permission_' . $user->getId() . '_' . $permission->getId(), $request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');
            throw new AccessDeniedHttpException();
        }

        $service->handleRevokePermission($user, $permission);

        return $this->redirectToRoute('admin_manage', ['user' => $user->getId()]);
    }
}
