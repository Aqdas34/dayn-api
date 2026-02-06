<?php

namespace App\Repository;

use App\Entity\DebtCollection;
use App\Entity\User;
use App\Enum\DebtCollectionConfirmationStatus;
use App\Enum\DebtCollectionStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @extends ServiceEntityRepository<DebtCollection>
 */
class DebtCollectionRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly LoggerInterface $logger
    )
    {
        parent::__construct($registry, DebtCollection::class);
    }

    //    /**
    //     * @return DebtCollection[] Returns an array of DebtCollection objects
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

    //    public function findOneBySomeField($value): ?DebtCollection
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
     * @return array|DebtCollection[]
     */
    public function listDebtCollectionsByUser(User $user, DebtCollectionStatus $status = null, DebtCollectionConfirmationStatus $confirmationStatus = null): array
    {
        $queryBuilder = $this->createQueryBuilder('d')
            ->where('(d.debtor = :user OR d.creditor = :user)')
            ->setParameter('user', $user);
        if ($status) {
            $queryBuilder->andWhere('d.status = :status')
                ->setParameter('status', $status);
        }
        if ($confirmationStatus) {
            $queryBuilder->andWhere('d.confirmationStatus = :confirmationStatus')
                ->setParameter('confirmationStatus', $confirmationStatus);
        }
        return $queryBuilder
            ->orderBy('d.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function sumAmountUnpaidByDebtor(User $debtor): int
    {
        return $this->createQueryBuilder('d')
            ->select('SUM(d.amountUnpaid)')
            ->where('d.debtor = :debtor')
            ->andWhere('d.status IN (:statuses)')
            ->andWhere('d.confirmationStatus = :confirmationStatus')
            ->setParameter('debtor', $debtor)
            ->setParameter('statuses', [
                DebtCollectionStatus::UNPAID->value,
                DebtCollectionStatus::PARTIAL->value,
            ])
            ->setParameter('confirmationStatus', DebtCollectionConfirmationStatus::ACCEPTED)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    public function sumAmountUnpaidByCreditor(User $creditor): int
    {
        $query = $this->createQueryBuilder('d')
            ->select('SUM(d.amountUnpaid)')
            ->where('d.creditor = :creditor')
            ->andWhere('d.status IN (:statuses)')
            ->andWhere('d.confirmationStatus = :confirmationStatus')
            ->setParameter('creditor', $creditor)
            ->setParameter('statuses', [
                DebtCollectionStatus::UNPAID->value,
                DebtCollectionStatus::PARTIAL->value,
            ])
            ->setParameter('confirmationStatus', DebtCollectionConfirmationStatus::ACCEPTED)
            ->getQuery();
        $this->logger->error($query->getSQL());
        return $query
            ->getSingleScalarResult() ?? 0;
    }
}
