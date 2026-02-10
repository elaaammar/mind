<?php

namespace App\Repository;

use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Role>
 */
class RoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    /**
     * Recherche des rôles par nom ou description
     */
    public function search(string $search = '', string $sortBy = 'nom', string $order = 'ASC'): array
    {
        $qb = $this->createQueryBuilder('r');

        if ($search) {
            $qb->andWhere('r.nom LIKE :search OR r.description LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        $qb->orderBy('r.' . $sortBy, $order);

        return $qb->getQuery()->getResult();
    }

    /**
     * Compte le nombre d'utilisateurs par rôle
     */
    public function countUtilisateursParRole(): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.id, r.nom, COUNT(u.id) as nbUtilisateurs')
            ->leftJoin('r.utilisateurs', 'u')
            ->groupBy('r.id')
            ->getQuery()
            ->getResult();
    }
}
