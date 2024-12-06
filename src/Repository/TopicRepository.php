<?php

namespace App\Repository;

use App\Entity\Topic;
use App\Entity\User;
use App\Service\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Topic>
 */
class TopicRepository extends ServiceEntityRepository
{
    private const ALIAS = 't';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Topic::class);
    }

    public function findPaginatedTopics(int $page, int $boardId, string $searchQuery = null): Paginator
    {
        $query = $this->createQueryBuilder(self::ALIAS)
            ->andWhere(self::ALIAS . '.board = :boardId')
            ->setParameter('boardId', $boardId)
            ->addOrderBy(self::ALIAS . '.createdAt', 'DESC');

        if ($searchQuery) {
            $query->andWhere(self::ALIAS . '.title LIKE :searchQuery')
                ->setParameter('searchQuery', '%' . $searchQuery . '%');
        }

        return new Paginator($page, $query);
    }

    public function findPaginatedUserTopics(int $page, User $user, string $searchQuery = null): Paginator
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

    public function findLatestUserTopics(User $user, int $limit = 5): array
    {
        return $this->createQueryBuilder(self::ALIAS)
            ->join(self::ALIAS . '.author', 'u')
            ->where( self::ALIAS . '.author = :author')
            ->setParameter('author', $user)
            ->orderBy(self::ALIAS . '.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
