<?php

namespace App\Repository;

use App\Entity\Permission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Permission>
 */
class PermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permission::class);
    }

    public function findNotOwnedPermissions($roleId): array
    {
        $qb = $this->createQueryBuilder('p');
        $subQuery = $this->createQueryBuilder('p2')
            ->select('p2.id')
            ->join('p2.roles', 'r')
            ->where('r.id = :roleId');

        return $qb->select('p')
            ->where($qb->expr()->notIn('p.id', $subQuery->getDQL()))
            ->setParameter('roleId', $roleId)
            ->getQuery()
            ->getResult();
    }
}
