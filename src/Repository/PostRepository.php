<?php

namespace App\Repository;

use App\Entity\Post;
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
            ->setParameter('topicId', $topicId);

        return new Paginator($page, $query);
    }

    //    public function findOneBySomeField($value): ?Post
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
