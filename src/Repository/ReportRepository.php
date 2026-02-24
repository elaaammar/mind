<?php

namespace App\Repository;

use App\Entity\Report;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Report>
 *
 * @method Report|null find($id, $lockMode = null, $lockVersion = null)
 * @method Report|null findOneBy(array $criteria, array $orderBy = null)
 * @method Report[]    findAll()
 * @method Report[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

//    /**
//     * @return Report[] Returns an array of Report objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }
    /**
     * Finds reports matching the search term restricted by source.
     */
    public function findBySearchAndSource(string $query, string $source): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('(r.title LIKE :query OR r.description LIKE :query)')
            ->andWhere('r.source = :source')
            ->setParameter('query', '%' . $query . '%')
            ->setParameter('source', $source)
            ->orderBy('r.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Finds reports matching the search term across all sources.
     */
    public function findBySearch(string $query): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('(r.title LIKE :query OR r.description LIKE :query)')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('r.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
