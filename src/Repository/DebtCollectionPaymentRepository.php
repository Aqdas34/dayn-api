<?php

namespace App\Repository;

use App\Entity\DebtCollectionPayment;
use App\Entity\User;
use App\Enum\DebtCollectionPaymentStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DebtCollectionPayment>
 */
class DebtCollectionPaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DebtCollectionPayment::class);
    }

    //    /**
    //     * @return DebtCollectionPayment[] Returns an array of DebtCollectionPayment objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?DebtCollectionPayment
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * @param User $user
     * @return array|DebtCollectionPayment[]
     */
    public function listAllCollectionPaymentByUser(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.debtCollection', 'debtCollection')
            ->where('(debtCollection.debtor = :user OR debtCollection.creditor = :user)')
            ->andWhere('p.status IN (:statuses)')
            ->setParameter('user', $user)
            ->setParameter('statuses', [
                DebtCollectionPaymentStatus::PENDING->value,
                DebtCollectionPaymentStatus::APPROVED->value,
            ])
            ->andWhere('p.status = :pendingStatus OR (p.status = :approvedStatus AND (p.isAcknowledged IS NULL OR p.isAcknowledged = false))')
            ->setParameter('pendingStatus', DebtCollectionPaymentStatus::PENDING->value)
            ->setParameter('approvedStatus', DebtCollectionPaymentStatus::APPROVED->value)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
