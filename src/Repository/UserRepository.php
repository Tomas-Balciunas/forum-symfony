<?php

namespace App\Repository;

use App\Entity\User;
use App\Service\Misc\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findPaginatedUsers(int $page, string $searchQuery = null): Paginator
    {
        $query = $this->createQueryBuilder('u')
            ->addOrderBy('u.username', 'ASC');

        if (null !== $searchQuery) {
            $query->andWhere('u.username LIKE :searchQuery')
            ->setParameter('searchQuery', '%' . $searchQuery . '%');
        }

        $count = $this->createQueryBuilder('u2')
            ->select('COUNT(u2.id)');

        return new Paginator($page, $query, $count, 10, 1, 1);
    }

    public function findLatestUsers(int $limit = 5): array
    {
        return $this->createQueryBuilder('u')
            ->select('u.id, u.username, u.createdAt')
            ->addOrderBy('u.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findHighestPostCount(int $limit = 5): array
    {
        return $this->createQueryBuilder('u')
            ->join('u.posts', 'p')
            ->select('u.id, u.username, COUNT(p.id) as postCount')
            ->orderBy('postCount', 'DESC')
            ->groupBy('u.id')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();


    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}
