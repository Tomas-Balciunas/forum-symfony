<?php

namespace App\Controller;

use App\Data\Permissions;
use App\Entity\User;
use App\Entity\UserSuspension;
use App\Exception\Suspension\ModifySuspensionException;
use App\Exception\Suspension\SuspendUserException;
use App\Form\PermanentSuspendType;
use App\Form\SuspendType;
use App\Form\SuspensionModifyType;
use App\Helper\SuspensionHelper;
use App\Service\Misc\AddFlashMessages;
use App\Service\Misc\PermissionAuthorization;
use App\Service\SuspensionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/suspension')]
class SuspensionController extends AbstractController
{
    public function __construct(
        private readonly PermissionAuthorization $authorize,
        private readonly AddFlashMessages        $addFlashMessages,
    ) {}

    #[Route('/{id}', name: 'show_suspension', methods: ['GET', 'POST'])]
    public function showSuspension(UserSuspension $suspension, SuspensionHelper $helper): Response
    {
        $dto = $helper->createSuspensionModifyDto($suspension);

        $modifyForm = $this->createForm(SuspensionModifyType::class, $dto, [
            'action' => $this->generateUrl('modify_suspension', ['id' => $suspension->getId()]),
            'method' => 'POST',
        ]);

        return $this->render('admin/suspension.html.twig', [
            'suspension' => $suspension,
            'modifyForm' => $modifyForm->createView(),
        ]);
    }

    #[Route('/{id}/modify', name: 'modify_suspension', methods: ['POST'])]
    public function modifySuspension(UserSuspension $suspension, SuspensionService $service, Request $request): Response
    {
        $modifyForm = $this->createForm(SuspensionModifyType::class);

        try {
            $this->authorize->permission(Permissions::USER_BAN_MODIFY);
            $modifyForm->handleRequest($request);

            if ($modifyForm->isSubmitted() && $modifyForm->isValid()) {
                $service->handleModifySuspension($suspension, $modifyForm->getData());
                $this->addFlashMessages->addSuccessMessage('Suspension modified.');
            }

        } catch (AccessDeniedException $e) {
            $this->addFlashMessages->addErrorMessage($e->getMessage());
        } catch (ModifySuspensionException $e) {
            $this->addFlashMessages->addErrorMessages($e->getExceptionErrors());
        } finally {
            return $this->redirectToRoute('show_suspension', ['id' => $suspension->getId()]);
        }
    }

    #[Route('/{id}/lift', name: 'lift_suspension')]
    public function liftSuspension(UserSuspension $suspension, SuspensionService $service): Response
    {
        try {
            $this->authorize->permission(Permissions::USER_UNBAN);
            $service->handleLiftSuspension($suspension);
            $this->addFlashMessages->addSuccessMessage('Suspension lifted.');
        } catch (AccessDeniedException $e) {
            $this->addFlashMessages->addErrorMessage($e->getMessage());
        } finally {
            return $this->redirectToRoute('admin_manage', ['user' => $suspension->getIssuedFor()->getId()]);
        }
    }

    #[Route('/suspend/{user}', name: 'suspend_user', methods: ['POST'])]
    public function suspendUser(User $user, SuspensionService $service, Request $request, #[CurrentUser] User $issuedBy): Response
    {
        $form = $this->createForm(SuspendType::class);

        try {
            $this->authorize->permission(Permissions::USER_BAN);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $service->handleSuspendUser($issuedBy, $user, $form->getData());
                $this->addFlashMessages->addSuccessMessage('User has been suspended.');
            }
        } catch (AccessDeniedException $e) {
            $this->addFlashMessages->addErrorMessage($e->getMessage());
        } catch (SuspendUserException $e) {
            $this->addFlashMessages->addErrorMessages($e->getExceptionErrors());
        } finally {
            return $this->redirectToRoute('admin_manage', ['user' => $user->getId()]);
        }
    }

    #[Route('/suspend-permanent/{user}', name: 'suspend_user_permanent', methods: ['POST'])]
    public function suspendUserPermanently(User $user, SuspensionService $service, Request $request, #[CurrentUser] User $issuedBy): Response
    {
        $form = $this->createForm(PermanentSuspendType::class);

        try {
            $this->authorize->permission(Permissions::USER_BAN);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $service->handleSuspendUserPermanently($issuedBy, $user, $data);
                $this->addFlashMessages->addSuccessMessage('User has been suspended permanently.');
            }
        } catch (AccessDeniedException $e) {
            $this->addFlashMessages->addErrorMessage($e->getMessage());
        } catch (SuspendUserException $e) {
            $this->addFlashMessages->addErrorMessages($e->getExceptionErrors());
        } finally {
            return $this->redirectToRoute('admin_manage', ['user' => $user->getId()]);
        }
    }
}
