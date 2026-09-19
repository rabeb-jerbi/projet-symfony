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

    //    /**
    //     * @return Voiture[] Returns an array of Voiture objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Voiture
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
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
