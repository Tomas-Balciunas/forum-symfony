<?php

namespace App\Controller;

use App\Data\Permissions;
use App\Entity\Permission;
use App\Entity\User;
use App\Exception\ValidationExceptionInterface;
use App\Service\PermissionAuthorization;
use App\Service\PermissionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

#[Route('/permission')]
class PermissionController extends AbstractController
{
    public function __construct(private readonly PermissionAuthorization $authorization)
    {
    }

    #[Route('/{permission}/permit/{user}', name: 'grant_permission')]
    public function grantPermission(User $user, Permission $permission, PermissionService $service, Request $request): Response
    {
        try {
//            $this->authorization->authorize(Permissions::USER_ADD_PERMISSION);

            if (!$this->isCsrfTokenValid('permit_user_' . $user->getId() . '_' . $permission->getId(), $request->get('_token'))) {
                $this->addFlash('error', 'Invalid CSRF token.');
                throw new InvalidCsrfTokenException();
            }

            $service->handleGrantPermission($user, $permission);
        } catch (ValidationExceptionInterface | AccessDeniedException $e) {
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
                throw new InvalidCsrfTokenException();
            }

            $service->handleRevokePermission($user, $permission);
        } catch (ValidationExceptionInterface $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_manage', ['user' => $user->getId()]);
    }
}