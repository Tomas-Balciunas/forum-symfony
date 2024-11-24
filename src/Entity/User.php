<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct()
    {
        $this->topics = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->permissions = new ArrayCollection();
        $this->issuedSuspensions = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $username = null;

    #[ORM\Column]
    private ?string $email = null;

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private int $postCount = 0;

    #[ORM\Column]
    private bool $isPrivate = false;

    #[ORM\Column]
    private string $status = 'active';

    #[ORM\OneToMany(targetEntity: Topic::class, mappedBy: 'author')]
    private Collection $topics;

    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'author')]
    private Collection $posts;

    #[ORM\ManyToOne(targetEntity: Role::class, inversedBy: 'users')]
    private Role $role;

    #[ORM\OneToOne(targetEntity: UserSuspension::class, mappedBy: 'issuedFor')]
    private UserSuspension $suspension;

    #[ORM\OneToMany(targetEntity: UserSuspension::class, mappedBy: 'issuedBy')]
    private Collection $issuedSuspensions;

    #[ORM\ManyToMany(targetEntity: Permission::class, inversedBy: 'users')]
    #[ORM\JoinTable('user_permission')]
    private Collection $permissions;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function isPrivate(): bool
    {
        return $this->isPrivate;
    }

    public function setIsPrivate(bool $isPrivate): void
    {
        $this->isPrivate = $isPrivate;
    }

    public function setRole(Role $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getRoles(): array
    {
        return [$this->role->getName()];
    }

    public function getDefaultPermissions(): Collection
    {
        return $this->role->getPermissions();
    }

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function setPermission(Permission $permission): static
    {
        $this->permissions->add($permission);

        return $this;
    }

    public function setPermissions(Collection $permissions): static
    {
        $this->permissions = $permissions;

        return $this;
    }

    public function getIssuedSuspensions(): Collection
    {
        return $this->issuedSuspensions;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function incrementPostCount(): static
    {
        $this->postCount++;

        return $this;
    }

    public function getPostCount(): int
    {
        return $this->postCount;
    }

    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        $this->posts[] = $post;

        return $this;
    }

    public function removePost(Post $post): static
    {
        $this->posts->removeElement($post);

        return $this;
    }

    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function addTopic(Topic $topic): static
    {
        $this->topics[] = $topic;

        return $this;
    }

    public function removeTopic(Topic $topic): static
    {
        $this->topics->removeElement($topic);

        return $this;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

}
