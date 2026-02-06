<?php

namespace App\Repository;

use App\Entity\Beneficiary;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Beneficiary>
 */
class BeneficiaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Beneficiary::class);
    }

    /**
     * @param User $user
     * @return Beneficiary[]
     */
    public function findByUserSorted(User $user): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.user = :user')
            ->setParameter('user', $user)
            ->orderBy('b.displayName', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
