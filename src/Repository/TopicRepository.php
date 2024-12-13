<?php

namespace App\Repository;

use App\Entity\Topic;
use App\Entity\User;
use App\Service\Misc\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Topic>
 */
class TopicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Topic::class);
    }

    public function findPaginatedTopics(int $page, int $boardId, string $searchQuery = null): Paginator
    {
        $qb = $this->createQueryBuilder('t');

        $query = $qb->select('t as topic')
            ->andWhere('t.board = :boardId')
            ->setParameter('boardId', $boardId)
            ->leftJoin('t.posts', 'p')
            ->addSelect($qb->expr()->count('p.id') . 'as post_count')
            ->addSelect($qb->expr()->max('p.createdAt') . 'as latest_post')
            ->addOrderBy($qb->expr()->max('p.createdAt'), 'DESC')
            ->addGroupBy('t.id');

        if ($searchQuery) {
            $query->andWhere('t.title LIKE :searchQuery')
                ->setParameter('searchQuery', '%' . $searchQuery . '%');
        }

        $count = $this->createQueryBuilder('t2')
            ->select('count(t2.id)')
            ->andWhere('t2.board = :boardId')
            ->setParameter('boardId', $boardId);

        return new Paginator($page, $query, $count);
    }

    public function findPaginatedUserTopics(int $page, User $user, string $searchQuery = null): Paginator
    {
        $query = $this->createQueryBuilder('t')->join('t.author', 'u')
            ->andWhere('t.author = :author')
            ->setParameter('author', $user)
            ->addOrderBy('t.createdAt', 'DESC');

        if ($searchQuery) {
            $query->andWhere('t.title LIKE :searchQuery')
                ->setParameter('searchQuery', '%' . $searchQuery . '%');
        }

        $count = $this->createQueryBuilder('t2')
            ->select('count(t2.id)')
            ->andWhere('t2.author = :author')
            ->setParameter('author', $user);

        return new Paginator($page, $query, $count);
    }

    public function findLatestUserTopics(User $user, int $limit = 5): array
    {
        return $this->createQueryBuilder('t')->join('t . author', 'u')
            ->where('t . author = :author')
            ->setParameter('author', $user)
            ->orderBy('t . createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
