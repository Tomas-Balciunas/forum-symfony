<?php

namespace App\Entity;

use App\Repository\UserSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSettingsRepository::class)]
#[ORM\UniqueConstraint(fields: ['user'])]
class UserSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $showEmail = true;

    #[ORM\Column]
    private ?bool $showPosts = true;

    #[ORM\Column]
    private ?bool $showTopics = true;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'settings')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isShowEmail(): ?bool
    {
        return $this->showEmail;
    }

    public function setShowEmail(bool $showEmail): static
    {
        $this->showEmail = $showEmail;

        return $this;
    }

    public function isShowPosts(): ?bool
    {
        return $this->showPosts;
    }

    public function setShowPosts(bool $showPosts): static
    {
        $this->showPosts = $showPosts;

        return $this;
    }

    public function isShowTopics(): ?bool
    {
        return $this->showTopics;
    }

    public function setShowTopics(bool $showTopics): static
    {
        $this->showTopics = $showTopics;

        return $this;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }
}
