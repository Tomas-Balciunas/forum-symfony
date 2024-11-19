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

        /**
         * @return Board[] Returns an array of Board objects
         */
        public function findByExampleField($value): array
        {
            return $this->createQueryBuilder('g')
                ->andWhere('g.exampleField = :val')
                ->setParameter('val', $value)
                ->orderBy('g.id', 'ASC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult()
            ;
        }
}