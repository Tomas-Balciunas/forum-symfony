<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserSuspension;
use App\Exception\ValidationExceptionInterface;
use App\Form\PermanentSuspendType;
use App\Form\SuspendType;
use App\Form\SuspensionModifyType;
use App\Helper\SuspensionHelper;
use App\Service\SuspensionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/suspension')]
class SuspensionController extends AbstractController
{
    #[Route('/{id}', name: 'show_suspension', methods: ['GET', 'POST'])]
    public function showSuspension(UserSuspension $suspension): Response
    {
        $dto = SuspensionHelper::createSuspensionModifyDto($suspension);

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
        try {
            $modifyForm = $this->createForm(SuspensionModifyType::class);
            $modifyForm->handleRequest($request);

            if ($modifyForm->isSubmitted() && $modifyForm->isValid()) {
                $service->handleModifySuspension($suspension, $modifyForm->getData());
            }

        } catch (ValidationExceptionInterface $e) {
            $this->addFlash('error', $e->getMessage());
        }


        return $this->redirectToRoute('show_suspension', ['id' => $suspension->getId()]);
    }

    #[Route('/{id}/lift', name: 'lift_suspension')]
    public function liftSuspension(UserSuspension $suspension, SuspensionService $service): Response
    {
        $service->handleLiftSuspension($suspension);

        return $this->redirectToRoute('admin_manage', ['user' => $suspension->getIssuedFor()->getId()]);
    }

    #[Route('/suspend/{user}', name: 'suspend_user', methods: ['POST'])]
    public function suspendUser(User $user, SuspensionService $service, Request $request, #[CurrentUser] User $issuedBy): Response
    {
        try {
            $form = $this->createForm(SuspendType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $service->handleSuspendUser($issuedBy, $user, $data);
            }
        } catch (ValidationExceptionInterface $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_manage', ['user' => $user->getId()]);
    }

    #[Route('/suspend-permanent/{user}', name: 'suspend_user_permanent', methods: ['POST'])]
    public function suspendUserPermanently(User $user, SuspensionService $service, Request $request, #[CurrentUser] User $issuedBy): Response
    {
        try {
            $form = $this->createForm(PermanentSuspendType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $service->handleSuspendUserPermanently($issuedBy, $user, $data);
            }
        } catch (ValidationExceptionInterface $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_manage', ['user' => $user->getId()]);
    }
}
