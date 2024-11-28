<?php

namespace App\Entity;

use App\Repository\PermissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
class Permission
{
    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $formattedName = null;

    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'permissions')]
    private Collection $roles;

    #[ManyToMany(targetEntity: User::class, mappedBy: 'permissions')]
    private Collection $users;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getFormattedName(): ?string
    {
        return $this->formattedName;
    }

    public function setFormattedName(?string $formattedName): void
    {
        $this->formattedName = $formattedName;
    }

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }
}
