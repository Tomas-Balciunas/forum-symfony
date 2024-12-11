<?php

namespace App\Service;

use App\Entity\DTO\SuspensionModifyDTO;
use App\Entity\User;
use App\Entity\UserSuspension;
use App\Exception\Suspension\ModifySuspensionException;
use App\Exception\Suspension\SuspendUserException;
use App\Helper\SuspensionHelper;
use App\Validate\Suspension\Finite\FiniteSuspensionValidator;
use App\Validate\Suspension\Modify\ModifySuspensionValidator;
use App\Validate\Suspension\Permanent\PermanentSuspensionValidator;
use Doctrine\ORM\EntityManagerInterface;

readonly class SuspensionService
{
    public function __construct(
        private EntityManagerInterface       $entityManager,
        private SuspensionHelper             $suspensionHelper,
        private FiniteSuspensionValidator    $finiteSuspensionValidator,
        private ModifySuspensionValidator    $modifySuspensionValidator,
        private PermanentSuspensionValidator $permanentSuspensionValidator
    )
    {
    }

    /**
     * @throws ModifySuspensionException
     */
    public function handleModifySuspension(UserSuspension $suspension, SuspensionModifyDTO $dto): void
    {
        if (!$dto->getIsPermanent()) {
            $errors = $this->modifySuspensionValidator->validate($dto->getExpiresAt())->getErrors();

            if (!empty($errors)) {
                throw new ModifySuspensionException($errors);
            }

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

    /**
     * @throws SuspendUserException
     */
    public function handleSuspendUser(User $issuedBy, User $issuedFor, mixed $data): void
    {
        ['days' => $d, 'hours' => $h, 'reason' => $reason] = $data;

        $errors = $this->finiteSuspensionValidator->validate([$d, $h], $issuedFor)->getErrors();

        if (!empty($errors)) {
            throw new SuspendUserException($errors);
        }

        $until = $this->suspensionHelper->makeSuspensionDate($d ?? 0, $h ?? 0);
        $suspension = $this->suspensionHelper->makeSuspensionFoundation($issuedBy, $issuedFor, $reason);
        $suspension->setExpiresAt($until);

        $this->entityManager->persist($suspension);
        $this->entityManager->flush();
    }

    /**
     * @throws SuspendUserException
     */
    public function handleSuspendUserPermanently(User $issuedBy, User $issuedFor, mixed $data): void
    {
        $errors = $this->permanentSuspensionValidator->validate($issuedFor)->getErrors();

        if (!empty($errors)) {
            throw new SuspendUserException($errors);
        }

        ['reason' => $reason] = $data;

        $suspension = $this->suspensionHelper->makeSuspensionFoundation($issuedBy, $issuedFor, $reason);
        $suspension->setIsPermanent(true);

        $this->entityManager->persist($suspension);
        $this->entityManager->flush();
    }
}