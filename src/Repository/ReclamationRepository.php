<?php

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reclamation>
 */
class ReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }

    public function createQueryBuilderWithFilters(
        ?string $search,
        ?string $statut,
        ?string $priorite,
        ?string $categorie,
        ?\DateTime $dateFrom,
        ?\DateTime $dateTo
    ): QueryBuilder {
        $qb = $this->createQueryBuilder('r')
            ->orderBy('r.dateCreation', 'DESC');

        if ($search) {
            $qb->andWhere('r.titre LIKE :search OR r.description LIKE :search OR r.nom LIKE :search OR r.email LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($statut) {
            $qb->andWhere('r.statut = :statut')
                ->setParameter('statut', $statut);
        }

        if ($priorite) {
            $qb->andWhere('r.priorite = :priorite')
                ->setParameter('priorite', $priorite);
        }

        if ($categorie) {
            $qb->andWhere('r.categorie = :categorie')
                ->setParameter('categorie', $categorie);
        }

        if ($dateFrom) {
            $qb->andWhere('r.dateCreation >= :dateFrom')
                ->setParameter('dateFrom', $dateFrom);
        }

        if ($dateTo) {
            $qb->andWhere('r.dateCreation <= :dateTo')
                ->setParameter('dateTo', $dateTo);
        }

        return $qb;
    }

    public function findDistinctCategories(): array
    {
        $result = $this->createQueryBuilder('r')
            ->select('DISTINCT r.categorie')
            ->where('r.categorie IS NOT NULL')
            ->orderBy('r.categorie', 'ASC')
            ->getQuery()
            ->getResult();

        return array_column($result, 'categorie');
    }

    /**
     * Count reclamations by status
     */
    public function countByStatut(): array
    {
        $result = $this->createQueryBuilder('r')
            ->select('r.statut, COUNT(r.id) as total')
            ->groupBy('r.statut')
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($result as $row) {
            $data[$row['statut']] = (int) $row['total'];
        }

        return $data;
    }

    /**
     * Get statistics by statut (alias for countByStatut)
     */
    public function getStatisticsByStatut(): array
    {
        return $this->countByStatut();
    }

    /**
     * Count reclamations by priority
     */
    public function countByPriorite(): array
    {
        $result = $this->createQueryBuilder('r')
            ->select('r.priorite, COUNT(r.id) as total')
            ->where('r.priorite IS NOT NULL')
            ->groupBy('r.priorite')
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($result as $row) {
            $data[$row['priorite']] = (int) $row['total'];
        }

        return $data;
    }

    /**
     * Get statistics by priorite (alias for countByPriorite)
     */
    public function getStatisticsByPriorite(): array
    {
        return $this->countByPriorite();
    }

    /**
     * Count reclamations by date for the last N days
     * Returns array with date and count
     */
    public function countByDate(int $days): array
    {
        $dateFrom = new \DateTime("-{$days} days");
        $dateFrom->setTime(0, 0, 0);

        // Get all reclamations in the date range
        $reclamations = $this->createQueryBuilder('r')
            ->select('r.dateCreation')
            ->where('r.dateCreation >= :dateFrom')
            ->setParameter('dateFrom', $dateFrom)
            ->orderBy('r.dateCreation', 'ASC')
            ->getQuery()
            ->getResult();

        // Group by date in PHP
        $dateCounts = [];
        foreach ($reclamations as $reclamation) {
            $date = $reclamation['dateCreation']->format('Y-m-d');
            if (!isset($dateCounts[$date])) {
                $dateCounts[$date] = 0;
            }
            $dateCounts[$date]++;
        }

        // Convert to the expected format
        $result = [];
        foreach ($dateCounts as $date => $count) {
            $result[] = [
                'date' => $date,
                'count' => $count
            ];
        }

        return $result;
    }

    /**
     * Count reclamations by category
     */
    public function countByCategorie(): array
    {
        $result = $this->createQueryBuilder('r')
            ->select('r.categorie, COUNT(r.id) as total')
            ->where('r.categorie IS NOT NULL')
            ->groupBy('r.categorie')
            ->orderBy('total', 'DESC')
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($result as $row) {
            $data[$row['categorie']] = (int) $row['total'];
        }

        return $data;
    }

    /**
     * Count reclamations by month for a given year
     */
    public function countByMonth(int $year): array
    {
        $dateFrom = new \DateTime("{$year}-01-01 00:00:00");
        $dateTo = new \DateTime("{$year}-12-31 23:59:59");

        // Get all reclamations for the year
        $reclamations = $this->createQueryBuilder('r')
            ->select('r.dateCreation')
            ->where('r.dateCreation BETWEEN :dateFrom AND :dateTo')
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo)
            ->getQuery()
            ->getResult();

        // Initialize all months with 0
        $data = array_fill(1, 12, 0);

        // Count by month in PHP
        foreach ($reclamations as $reclamation) {
            $month = (int) $reclamation['dateCreation']->format('n');
            $data[$month]++;
        }

        return $data;
    }

    /**
     * Count recent reclamations (last N days)
     */
    public function countRecent(int $days): int
    {
        $dateFrom = new \DateTime("-{$days} days");

        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.dateCreation >= :dateFrom')
            ->setParameter('dateFrom', $dateFrom)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Count reclamations in a date range
     */
    public function countByDateRange(\DateTime $dateFrom, \DateTime $dateTo): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.dateCreation BETWEEN :dateFrom AND :dateTo')
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Count by status in date range
     */
    public function countByStatutInRange(\DateTime $dateFrom, \DateTime $dateTo): array
    {
        $result = $this->createQueryBuilder('r')
            ->select('r.statut, COUNT(r.id) as total')
            ->where('r.dateCreation BETWEEN :dateFrom AND :dateTo')
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo)
            ->groupBy('r.statut')
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($result as $row) {
            $data[$row['statut']] = (int) $row['total'];
        }

        return $data;
    }

    /**
     * Count by priority in date range
     */
    public function countByPrioriteInRange(\DateTime $dateFrom, \DateTime $dateTo): array
    {
        $result = $this->createQueryBuilder('r')
            ->select('r.priorite, COUNT(r.id) as total')
            ->where('r.dateCreation BETWEEN :dateFrom AND :dateTo')
            ->andWhere('r.priorite IS NOT NULL')
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo)
            ->groupBy('r.priorite')
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($result as $row) {
            $data[$row['priorite']] = (int) $row['total'];
        }

        return $data;
    }

    /**
     * Get top categories by count
     */
    public function getTopCategories(int $limit = 10): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.categorie, COUNT(r.id) as total')
            ->where('r.categorie IS NOT NULL')
            ->groupBy('r.categorie')
            ->orderBy('total', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Count reclamations that have at least one response
     */
    public function countWithResponses(): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(DISTINCT r.id)')
            ->innerJoin('r.reponses', 'rep')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Count by status and priority
     */
    public function countByStatutAndPriorite(string $statut, string $priorite): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.statut = :statut')
            ->andWhere('r.priorite = :priorite')
            ->setParameter('statut', $statut)
            ->setParameter('priorite', $priorite)
            ->getQuery()
            ->getSingleScalarResult();
    }
}