<?php

namespace App\Service;

use App\Entity\DTO\PostDTO;
use App\Entity\Post;
use App\Entity\Topic;
use App\Entity\User;
use App\Exception\Post\CreatePostException;
use App\Helper\PostHelper;
use App\Validate\Post\Create\CreatePostValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

readonly class PostService
{
    public function __construct(
        private EntityManagerInterface $manager,
        private CreatePostValidator $createPostValidator,
        private PostHelper $helper,
    ) {}

    /**
     * @throws CreatePostException
     */
    public function handleCreatePost(PostDTO $dto, Topic $topic, #[CurrentUser] User $user): Post
    {
        $errors = $this->createPostValidator->validate($topic, $user)->getErrors();

        if (!empty($errors)) {
            throw new CreatePostException($errors);
        }

        $post = $this->helper->makePost($dto);
        $post->setAuthor($user);
        $topic->addPost($post);

        $this->manager->flush();

        return $post;
    }

    public function handleUpdatePost(PostDTO $dto, Post $post): Post
    {
        $updatedPost = $this->helper->updatePost($dto, $post);

        $this->manager->flush();

        return $updatedPost;
    }
}