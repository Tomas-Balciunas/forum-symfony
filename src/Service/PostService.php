<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\Topic;
use App\Entity\User;
use App\Exception\Post\CreatePostException;
use App\Validate\Post\Create\CreatePostValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

readonly class PostService
{
    public function __construct(
        private EntityManagerInterface $manager,
        private CreatePostValidator $createPostValidator
    ) {}

    /**
     * @throws CreatePostException
     */
    public function handleCreatePost(Post $post, Topic $topic, #[CurrentUser] User $user): void
    {
        $errors = $this->createPostValidator->validate($topic)->getErrors();

        if (!empty($errors)) {
            throw new CreatePostException($errors);
        }

        $post->setAuthor($user);
        $topic->addPost($post);
        $this->manager->flush();
    }
}