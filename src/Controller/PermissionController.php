<?php

namespace App\Controller;

use App\Entity\Permission;
use App\Entity\User;
use App\Exception\ValidationExceptionInterface;
use App\Service\PermissionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/permission')]
class PermissionController extends AbstractController
{
    #[Route('/{permission}/permit/{user}', name: 'grant_permission')]
    public function grantPermission(User $user, Permission $permission, PermissionService $service, Request $request): Response
    {
        try {
            if (!$this->isCsrfTokenValid('permit_user_' . $user->getId() . '_' . $permission->getId(), $request->get('_token'))) {
                $this->addFlash('error', 'Invalid CSRF token.');
                throw new AccessDeniedHttpException();
            }

            $service->handleGrantPermission($user, $permission);
        } catch (ValidationExceptionInterface $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_manage', ['user' => $user->getId()]);
    }

    #[Route('/{permission}/revoke/{user}', name: 'revoke_permission')]
    public function revokePermission(User $user, Permission $permission, PermissionService $service, Request $request): Response
    {
        try {
            if (!$this->isCsrfTokenValid('revoke_permission_' . $user->getId() . '_' . $permission->getId(), $request->get('_token'))) {
                $this->addFlash('error', 'Invalid CSRF token.');
                throw new AccessDeniedHttpException();
            }

            $service->handleRevokePermission($user, $permission);
        } catch (ValidationExceptionInterface $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_manage', ['user' => $user->getId()]);
    }
}