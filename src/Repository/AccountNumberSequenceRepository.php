<?php

namespace App\Repository;

use App\Entity\AccountNumberSequence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AccountNumberSequenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccountNumberSequence::class);
    }

    public function getNextAccountNumber(string $prefix): string
    {
        $entityManager = $this->getEntityManager();

        // Fetch or create a new sequence entry for the given prefix
        $sequence = $this->find($prefix);
        if (!$sequence) {
            $sequence = new AccountNumberSequence();
            $sequence->setPrefix($prefix);
            $sequence->setLastAccountNumber(0);
        }

        // Increment and persist the last account number
        $lastAccountNumber = $sequence->getLastAccountNumber() + 1;
        $sequence->setLastAccountNumber($lastAccountNumber);

        $entityManager->persist($sequence);
        $entityManager->flush();

        // Format the account number as [prefix][7-digit-sequence]
        return sprintf('%s%07d', $prefix, $lastAccountNumber);
    }
}
