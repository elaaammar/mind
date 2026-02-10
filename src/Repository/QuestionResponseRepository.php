<?php

namespace App\Repository;

use App\Entity\QuestionResponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuestionResponse>
 *
 * @method QuestionResponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuestionResponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuestionResponse[]    findAll()
 * @method QuestionResponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionResponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionResponse::class);
    }
}
