<?php

namespace App\Service;

use App\Entity\DTO\SuspensionModifyDTO;
use App\Entity\User;
use App\Entity\UserSuspension;
use App\Helper\SuspensionHelper;
use App\Validate\Suspension\SuspensionValidator;
use Doctrine\ORM\EntityManagerInterface;

readonly class SuspensionService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function handleModifySuspension(UserSuspension $suspension, SuspensionModifyDTO $dto): void
    {
        SuspensionValidator::validateModifySuspension($dto);

        if (!$dto->getIsPermanent()) {
            $suspension->setExpiresAt($dto->getExpiresAt());
        }

        $suspension->setReason($dto->getReason());
        $suspension->setIsPermanent($dto->getIsPermanent());

        $this->entityManager->flush();
    }

    public function handleLiftSuspension(UserSuspension $suspension): void
    {
        $user = $suspension->getIssuedFor();
        $this->entityManager->remove($suspension);
        $user->setStatus('active');
        $this->entityManager->flush();
    }

    public function handleSuspendUser(User $issuedBy, User $issuedFor, mixed $data): void
    {
        ['days' => $d, 'hours' => $h, 'reason' => $reason] = $data;

        SuspensionValidator::validateSuspend([$d, $h], $issuedFor);

        $until = SuspensionHelper::getSuspensionDate($d ?? 0, $h ?? 0);
        $suspension = SuspensionHelper::makeSuspensionFoundation($issuedBy, $issuedFor, $reason);
        $suspension->setExpiresAt($until);

        $this->entityManager->persist($suspension);
        $this->entityManager->flush();
    }

    public function handleSuspendUserPermanently(User $issuedBy, User $issuedFor, mixed $data): void
    {
        ['reason' => $reason] = $data;

        SuspensionValidator::validateSuspendPermanently($issuedFor);

        $suspension = SuspensionHelper::makeSuspensionFoundation($issuedBy, $issuedFor, $reason);
        $suspension->setIsPermanent(true);

        $this->entityManager->persist($suspension);
        $this->entityManager->flush();
    }
}