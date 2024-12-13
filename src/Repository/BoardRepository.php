<?php

namespace App\Repository;

use App\Entity\Board;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Board>
 */
class BoardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Board::class);
    }

    public function findAllWithCount(): array
    {
        $qb = $this->createQueryBuilder('b');

        return $qb
            ->select('b as board')
            ->leftJoin('b.topics', 't')
            ->addSelect($qb->expr()->countDistinct('t.id') . 'as topic_count')
            ->leftJoin('t.posts', 'posts')
            ->addSelect($qb->expr()->count('posts.id') . 'as total_posts')
            ->addOrderBy('b.createdAt', 'ASC')
            ->addGroupBy('b.id')
            ->getQuery()
            ->getResult();
    }
}
