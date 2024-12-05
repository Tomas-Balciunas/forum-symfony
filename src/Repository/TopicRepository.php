<?php

namespace App\Repository;

use App\Entity\Topic;
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

        public function findPaginatedTopics(int $page, int $boardId): Paginator
        {
            $query = $this->createQueryBuilder(self::ALIAS)
                ->andWhere(self::ALIAS . '.board = :boardId')
                ->setParameter('boardId', $boardId)
            ;

            return new Paginator($page, $query);
        }
}
