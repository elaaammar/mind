<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Utilisateur>
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    /**
     * Recherche des utilisateurs par nom, prénom, email ou rôle
     */
    public function search(string $search = '', string $roleFilter = '', string $sortBy = 'nom', string $order = 'ASC'): array
    {
        $qb = $this->createQueryBuilder('u')
            ->leftJoin('u.role', 'r');

        if ($search) {
            $qb->andWhere('u.nom LIKE :search OR u.prenom LIKE :search OR u.email LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        if ($roleFilter) {
            $qb->andWhere('r.id = :roleId')
               ->setParameter('roleId', $roleFilter);
        }

        $qb->orderBy('u.' . $sortBy, $order);

        return $qb->getQuery()->getResult();
    }

    /**
     * Compte le nombre d'utilisateurs actifs
     */
    public function countActifs(): int
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.actif = :actif')
            ->setParameter('actif', true)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Trouve les utilisateurs par rôle
     */
    public function findByRole(int $roleId): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.role = :roleId')
            ->setParameter('roleId', $roleId)
            ->orderBy('u.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
