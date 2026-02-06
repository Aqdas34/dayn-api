<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserAccessToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserAccessToken>
 */
class UserAccessTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAccessToken::class);
    }

    //    /**
    //     * @return UserAccessToken[] Returns an array of UserAccessToken objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UserAccessToken
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function deleteAllRecords(): void
    {
        $this->createQueryBuilder('u')
            ->delete(UserAccessToken::class, 'u')
            ->getQuery()
            ->execute();
    }

    public function deleteByUser(User $user, string $deviceUid): void
    {
        $this->createQueryBuilder('u')
            ->delete(UserAccessToken::class, 'u')
            ->where('u.user = :user')
            ->andWhere('u.deviceUid = :deviceUid')
            ->setParameter('user', $user)
            ->setParameter('deviceUid', $deviceUid)
            ->getQuery()
            ->execute();
    }
}
