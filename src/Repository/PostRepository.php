<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use App\Service\Misc\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findPaginatedPosts(int $page, int $topicId): Paginator
    {
        $query = $this->createQueryBuilder('p')
            ->andWhere('p.topic = :topicId')
            ->setParameter('topicId', $topicId)
            ->addOrderBy('p.createdAt', 'ASC');

        $count = $this->createQueryBuilder('p2')
            ->select('count(p2.id)')
            ->andWhere('p2.topic = :topicId')
            ->setParameter('topicId', $topicId);

        return new Paginator($page, $query, $count);
    }

    public function findPaginatedUserPosts(int $page, User $user, string $searchQuery = null): Paginator
    {
        $query = $this->createQueryBuilder('p')
            ->join('p.author', 'u')
            ->andWhere('p.author = :author')
            ->setParameter('author', $user)
            ->addOrderBy('p.createdAt', 'DESC');

        if ($searchQuery) {
            $query->andWhere('p.title LIKE :searchQuery')
                ->setParameter('searchQuery', '%' . $searchQuery . '%');
        }

        $count = $this->createQueryBuilder('p2')
            ->select('count(p2.id)')
            ->andWhere('p2.author = :author')
            ->setParameter('author', $user);

        return new Paginator($page, $query, $count);
    }

    public function findLatestUserPosts(User $user, int $limit = 5): array
    {
        return $this->baseLatestPostsQuery($limit)
            ->join('p.author', 'u')
            ->where('p.author = :author')
            ->setParameter('author', $user)
            ->getQuery()
            ->getResult();
    }

    public function findLatestPosts(int $limit = 5): array
    {
        return $this->baseLatestPostsQuery($limit)
            ->getQuery()
            ->getResult();
    }

    private function baseLatestPostsQuery(int $limit): QueryBuilder
    {
        return $this->createQueryBuilder('p')
        ->addOrderBy('p.createdAt', 'DESC')
        ->setMaxResults($limit);
    }

    public function findPostPosInTopic(int $postId, int $topicId): int
    {
        return (int)$this->createQueryBuilder('p')
            ->join('p.topic', 't')
            ->select('count(p.id)')
            ->andWhere('p.id <= :postId')
            ->setParameter('postId', $postId)
            ->andWhere('p.topic = :topicId')
            ->setParameter('topicId', $topicId)
            ->getQuery()
            ->getSingleScalarResult();

    }
}
