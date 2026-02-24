<?php

namespace App\Repository;

use App\Entity\QuestionAudit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuestionAudit>
 *
 * @method QuestionAudit|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuestionAudit|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuestionAudit[]    findAll()
 * @method QuestionAudit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionAuditRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionAudit::class);
    }
}
