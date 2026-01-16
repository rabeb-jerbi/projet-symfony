<?php

namespace App\Repository;

use App\Entity\Voiture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Voiture>
 */
class VoitureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voiture::class);
    }

    /**
     * Recherche avancée de véhicules avec filtres et tri
     */
    public function findBySearchCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder('v');

        // Filtre par statut (par défaut: disponible)
        if (isset($criteria['statut']) && !empty($criteria['statut'])) {
            $qb->andWhere('v.statut = :statut')
               ->setParameter('statut', $criteria['statut']);
        }

        // Recherche par texte (marque ou modèle)
        if (isset($criteria['search']) && !empty($criteria['search'])) {
            $qb->andWhere('v.marque LIKE :search OR v.modele LIKE :search')
               ->setParameter('search', '%' . $criteria['search'] . '%');
        }

        // Filtre par type (basé sur la marque pour l'instant)
        if (isset($criteria['type']) && !empty($criteria['type'])) {
            switch ($criteria['type']) {
                case 'sedan':
                    $qb->andWhere('v.marque IN (:sedanBrands)')
                       ->setParameter('sedanBrands', ['Mercedes', 'BMW', 'Audi']);
                    break;
                case 'suv':
                    $qb->andWhere('v.marque IN (:suvBrands)')
                       ->setParameter('suvBrands', ['Range Rover', 'Porsche', 'Jeep']);
                    break;
                case 'luxe':
                    $qb->andWhere('v.prixAchat >= :luxePrice')
                       ->setParameter('luxePrice', 100000);
                    break;
            }
        }

        // Tri
        $sort = $criteria['sort'] ?? 'newest';
        switch ($sort) {
            case 'price_asc':
                $qb->orderBy('v.prixAchat', 'ASC');
                break;
            case 'price_desc':
                $qb->orderBy('v.prixAchat', 'DESC');
                break;
            case 'newest':
            default:
                $qb->orderBy('v.annee', 'DESC')
                   ->addOrderBy('v.id', 'DESC');
                break;
        }

        return $qb->getQuery()->getResult();
    }

    public function findAvailable(): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.statut = :statut')
            ->setParameter('statut', 'disponible')
            ->orderBy('v.marque', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function searchByKeyword(string $keyword): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.marque LIKE :keyword OR v.modele LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->getQuery()
            ->getResult();
    }

    public function countByStatut(string $statut): int
    {
        return $this->createQueryBuilder('v')
            ->select('COUNT(v.id)')
            ->where('v.statut = :statut')
            ->setParameter('statut', $statut)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
