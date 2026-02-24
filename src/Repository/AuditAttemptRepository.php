<?php

namespace App\Repository;

use App\Entity\AuditAttempt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AuditAttempt>
 *
 * @method AuditAttempt|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuditAttempt|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuditAttempt[]    findAll()
 * @method AuditAttempt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuditAttemptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuditAttempt::class);
    }
}
