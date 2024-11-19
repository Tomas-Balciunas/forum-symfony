<?php

namespace App\Entity;

use App\Repository\UserSuspensionRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSuspensionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class UserSuspension
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'suspension')]
    #[ORM\JoinColumn(name: 'issued_for', unique: true)]
    private User $issuedFor;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'issuedSuspensions')]
    #[ORM\JoinColumn(name: 'issued_by')]
    private User $issuedBy;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $reason = null;

    #[ORM\Column]
    private ?DateTimeImmutable $issuedAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTime $expiresAt = null;

    #[ORM\Column]
    private ?bool $isPermanent = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function getIssuedAt(): ?DateTimeImmutable
    {
        return $this->issuedAt;
    }

    #[ORM\PrePersist]
    public function setIssuedAt(): static
    {
        $this->issuedAt = new DateTimeImmutable('now');

        return $this;
    }

    public function getExpiresAt(): ?DateTime
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(DateTime|null $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function isPermanent(): ?bool
    {
        return $this->isPermanent;
    }

    public function setPermanent(): static
    {
        $this->isPermanent = true;
        $this->setExpiresAt(null);

        return $this;
    }
}
