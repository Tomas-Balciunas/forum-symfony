<?php

namespace App\Entity;

use App\Entity\Interface\AuthorInterface;
use App\Repository\TopicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TopicRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Topic implements AuthorInterface
{
    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $body = null;

    #[ORM\Column]
    private bool $isLocked = false;

    #[ORM\Column]
    private bool $isVisible = true;

    #[ORM\Column]
    private bool $isImportant = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'topic', cascade: ['persist', 'remove'])]
    private Collection $posts;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist', 'remove'], inversedBy: 'topics')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $author = null;

    #[ORM\ManyToOne(targetEntity: Board::class, inversedBy: 'topics')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Board $board = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): static
    {
        $this->createdAt = new \DateTimeImmutable('now');

        return $this;
    }

    public function getIsLocked(): bool
    {
        return $this->isLocked;
    }

    public function setIsLocked(bool $isLocked): static
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    public function getIsVisible(): bool
    {
        return $this->isVisible;
    }

    public function setIsVisible(bool $isVisible): static
    {
        $this->isVisible = $isVisible;

        return $this;
    }

    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        $post->setTopic($this);
        $this->posts->add($post);

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    public function getBoard(): ?Board
    {
        return $this->board;
    }

    public function setBoard(?Board $board): void
    {
        $this->board = $board;
    }

    public function getIsImportant(): bool
    {
        return $this->isImportant;
    }

    public function setIsImportant(bool $isImportant): void
    {
        $this->isImportant = $isImportant;
    }
}
