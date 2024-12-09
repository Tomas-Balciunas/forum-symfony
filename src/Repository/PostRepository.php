<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use App\Service\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    private const ALIAS = 'p';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findPaginatedPosts(int $page, int $topicId): Paginator
    {
        $query = $this->createQueryBuilder(self::ALIAS)
            ->andWhere(self::ALIAS . '.topic = :topicId')
            ->setParameter('topicId', $topicId)
            ->addOrderBy(self::ALIAS . '.createdAt', 'ASC');

        return new Paginator($page, $query);
    }

    public function findPaginatedUserPosts(int $page, User $user, string $searchQuery = null): Paginator
    {
        $query = $this->createQueryBuilder(self::ALIAS)
            ->join(self::ALIAS . '.author', 'u')
            ->andWhere(self::ALIAS . '.author = :author')
            ->setParameter('author', $user)
            ->addOrderBy(self::ALIAS . '.createdAt', 'DESC');

        if ($searchQuery) {
            $query->andWhere(self::ALIAS . '.title LIKE :searchQuery')
                ->setParameter('searchQuery', '%' . $searchQuery . '%');
        }

        return new Paginator($page, $query);
    }

    public function findLatestUserPosts(User $user, int $limit = 5): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.author', 'u')
            ->where('p.author = :author')
            ->setParameter('author', $user)
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findPostPosInTopic(int $postId, int $topicId): int
    {
        return $this->createQueryBuilder(self::ALIAS)
            ->join(self::ALIAS . '.topic', 't')
            ->select('count(1)')
            ->andWhere(self::ALIAS . '.id <= :postId')
            ->setParameter('postId', $postId)
            ->andWhere(self::ALIAS . '.topic = :topicId')
            ->setParameter('topicId', $topicId)
            ->getQuery()
            ->getSingleScalarResult();

    }
}
