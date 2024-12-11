<?php

namespace App\Controller;

use App\Data\Permissions;
use App\Entity\Permission;
use App\Entity\User;
use App\Exception\Permission\GrantPermissionException;
use App\Exception\Permission\RevokePermissionException;
use App\Service\Misc\AddFlashMessages;
use App\Service\PermissionAuthorization;
use App\Service\PermissionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/permission')]
class PermissionController extends AbstractController
{
    public function __construct(
        private readonly PermissionAuthorization $authorize,
        private readonly AddFlashMessages        $flashMessages
    ) {}

    #[Route('/{permission}/permit/{user}', name: 'grant_permission')]
    public function grantPermission(#[CurrentUser] User $grantedBy, User $user, Permission $permission, PermissionService $service, Request $request): Response
    {
        try {
            $this->authorize->permission(Permissions::USER_ADD_PERMISSION);

            if (!$this->isCsrfTokenValid('permit_user_' . $user->getId() . '_' . $permission->getId(), $request->get('_token'))) {
                $this->addFlash('error', 'Invalid CSRF token.');
                throw new InvalidCsrfTokenException();
            }

            $service->handleGrantPermission($user, $grantedBy, $permission);
        } catch (AccessDeniedException $e) {
            $this->flashMessages->addErrorMessage($e->getMessage());
        } catch (GrantPermissionException $e) {
            $this->flashMessages->addErrorMessages($e->getExceptionErrors());
        } finally {
            return $this->redirectToRoute('admin_manage', ['user' => $user->getId()]);
        }
    }

    #[Route('/{permission}/revoke/{user}', name: 'revoke_permission')]
    public function revokePermission(#[CurrentUser] User $revokedBy, User $user, Permission $permission, PermissionService $service, Request $request): Response
    {
        try {
            $this->authorize->permission(Permissions::USER_REVOKE_PERMISSION);

            if (!$this->isCsrfTokenValid('revoke_permission_' . $user->getId() . '_' . $permission->getId(), $request->get('_token'))) {
                $this->addFlash('error', 'Invalid CSRF token.');
                throw new InvalidCsrfTokenException();
            }

            $service->handleRevokePermission($user, $revokedBy, $permission);
        } catch (AccessDeniedException $e) {
            $this->flashMessages->addErrorMessage($e->getMessage());
        } catch (RevokePermissionException $e) {
            $this->flashMessages->addErrorMessages($e->getExceptionErrors());
        } finally {
            return $this->redirectToRoute('admin_manage', ['user' => $user->getId()]);
        }
    }
}