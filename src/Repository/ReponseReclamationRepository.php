<?php

namespace App\Repository;

use App\Entity\ReponseReclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReponseReclamation>
 */
class ReponseReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReponseReclamation::class);
    }

    public function createQueryBuilderWithFilters(
        ?string $search,
        ?int $reclamationId,
        ?\DateTime $dateFrom,
        ?\DateTime $dateTo,
        ?string $auteurType
        ): QueryBuilder
    {
        $qb = $this->createQueryBuilder('rr')
            ->leftJoin('rr.reclamation', 'r')
            ->addSelect('r')
            ->orderBy('rr.dateCreation', 'DESC');

        if ($search) {
            $qb->andWhere('rr.contenu LIKE :search OR rr.nom LIKE :search OR r.titre LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($reclamationId) {
            $qb->andWhere('rr.reclamation = :reclamationId')
                ->setParameter('reclamationId', $reclamationId);
        }

        if ($dateFrom) {
            $qb->andWhere('rr.dateCreation >= :dateFrom')
                ->setParameter('dateFrom', $dateFrom);
        }

        if ($dateTo) {
            $qb->andWhere('rr.dateCreation <= :dateTo')
                ->setParameter('dateTo', $dateTo);
        }

        if ($auteurType) {
            $qb->andWhere('rr.auteurType = :auteurType')
                ->setParameter('auteurType', $auteurType);
        }

        return $qb;
    }

    /**
     * Count responses by author type
     */
    public function countByAuteurType(): array
    {
        $result = $this->createQueryBuilder('rr')
            ->select('rr.auteurType, COUNT(rr.id) as total')
            ->where('rr.auteurType IS NOT NULL')
            ->groupBy('rr.auteurType')
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($result as $row) {
            $data[$row['auteurType']] = (int)$row['total'];
        }

        return $data;
    }

    /**
     * Count responses by date for the last N days
     * Returns array with date and count
     */
    public function countByDate(int $days): array
    {
        $dateFrom = new \DateTime("-{$days} days");
        $dateFrom->setTime(0, 0, 0);

        $results = $this->createQueryBuilder('rr')
            ->select('rr.dateCreation')
            ->where('rr.dateCreation >= :dateFrom')
            ->setParameter('dateFrom', $dateFrom)
            ->orderBy('rr.dateCreation', 'ASC')
            ->getQuery()
            ->getResult();

        $dateCounts = [];
        foreach ($results as $row) {
            $date = $row['dateCreation']->format('Y-m-d');
            if (!isset($dateCounts[$date])) {
                $dateCounts[$date] = 0;
            }
            $dateCounts[$date]++;
        }

        $data = [];
        foreach ($dateCounts as $date => $count) {
            $data[] = [
                'date' => $date,
                'count' => $count
            ];
        }

        return $data;
    }

    /**
     * Count responses by month for a given year
     */
    public function countByMonth(int $year): array
    {
        $dateFrom = new \DateTime("{$year}-01-01 00:00:00");
        $dateTo = new \DateTime("{$year}-12-31 23:59:59");

        $results = $this->createQueryBuilder('rr')
            ->select('rr.dateCreation')
            ->where('rr.dateCreation BETWEEN :dateFrom AND :dateTo')
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo)
            ->getQuery()
            ->getResult();

        $data = array_fill(1, 12, 0);
        foreach ($results as $row) {
            $month = (int)$row['dateCreation']->format('n');
            $data[$month]++;
        }

        return $data;
    }

    /**
     * Count recent responses (last N days)
     */
    public function countRecent(int $days): int
    {
        $dateFrom = new \DateTime("-{$days} days");

        return (int)$this->createQueryBuilder('rr')
            ->select('COUNT(rr.id)')
            ->where('rr.dateCreation >= :dateFrom')
            ->setParameter('dateFrom', $dateFrom)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Count responses in a date range
     */
    public function countByDateRange(\DateTime $dateFrom, \DateTime $dateTo): int
    {
        return (int)$this->createQueryBuilder('rr')
            ->select('COUNT(rr.id)')
            ->where('rr.dateCreation BETWEEN :dateFrom AND :dateTo')
            ->setParameter('dateFrom', $dateFrom)
            ->setParameter('dateTo', $dateTo)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get average response time in days
     * Calculates the average time between reclamation creation and first response
     */
    public function getAverageResponseTime(): float
    {
        // Get all responses with their reclamation dates
        $reponses = $this->createQueryBuilder('rr')
            ->select('rr.dateCreation as responseDate, r.dateCreation as reclamationDate')
            ->innerJoin('rr.reclamation', 'r')
            ->getQuery()
            ->getResult();

        if (empty($reponses)) {
            return 0.0;
        }

        $totalDays = 0;
        $count = 0;

        foreach ($reponses as $reponse) {
            $reclamationDate = $reponse['reclamationDate'];
            $responseDate = $reponse['responseDate'];

            if ($reclamationDate && $responseDate) {
                $diff = $responseDate->diff($reclamationDate);
                $totalDays += $diff->days;
                $count++;
            }
        }

        return $count > 0 ? round($totalDays / $count, 2) : 0.0;
    }

    /**
     * Get average number of responses per reclamation
     */
    public function getAverageResponsesPerReclamation(): float
    {
        $totalResponses = $this->count([]);

        $totalReclamationsWithResponses = (int)$this->createQueryBuilder('rr')
            ->select('COUNT(DISTINCT rr.reclamation)')
            ->getQuery()
            ->getSingleScalarResult();

        if ($totalReclamationsWithResponses === 0) {
            return 0.0;
        }

        return round($totalResponses / $totalReclamationsWithResponses, 2);
    }

    /**
     * Get responses grouped by reclamation with counts
     */
    public function getResponseCountsByReclamation(): array
    {
        return $this->createQueryBuilder('rr')
            ->select('r.id as reclamation_id, r.titre, COUNT(rr.id) as response_count')
            ->innerJoin('rr.reclamation', 'r')
            ->groupBy('r.id')
            ->orderBy('response_count', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find reclamations with most responses
     */
    public function findReclamationsWithMostResponses(int $limit = 10): array
    {
        return $this->createQueryBuilder('rr')
            ->select('r.id, r.titre, COUNT(rr.id) as total_responses')
            ->innerJoin('rr.reclamation', 'r')
            ->groupBy('r.id')
            ->orderBy('total_responses', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get response distribution by hour of day
     */
    public function getResponseDistributionByHour(): array
    {
        $results = $this->createQueryBuilder('rr')
            ->select('rr.dateCreation')
            ->getQuery()
            ->getResult();

        $data = array_fill(0, 24, 0);
        foreach ($results as $row) {
            if ($row['dateCreation']) {
                $hour = (int)$row['dateCreation']->format('G');
                $data[$hour]++;
            }
        }

        return $data;
    }

    /**
     * Get response distribution by day of week
     */
    public function getResponseDistributionByDayOfWeek(): array
    {
        $results = $this->createQueryBuilder('rr')
            ->select('rr.dateCreation')
            ->getQuery()
            ->getResult();

        $days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        $counts = array_fill(0, 7, 0);

        foreach ($results as $row) {
            if ($row['dateCreation']) {
                $dayIndex = (int)$row['dateCreation']->format('w');
                $counts[$dayIndex]++;
            }
        }

        $data = [];
        foreach ($days as $index => $dayName) {
            $data[$dayName] = $counts[$index];
        }

        return $data;
    }
}