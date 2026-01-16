<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }
    public function findRecentCommandes(int $limit = 10): array
{
    return $this->createQueryBuilder('c')
        ->orderBy('c.date', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
}

public function findByClient($client): array
{
    return $this->createQueryBuilder('c')
        ->where('c.client = :client')
        ->setParameter('client', $client)
        ->orderBy('c.date', 'DESC')
        ->getQuery()
        ->getResult();
}

public function countByStatut(string $statut): int
{
    return $this->createQueryBuilder('c')
        ->select('COUNT(c.id)')
        ->where('c.statut = :statut')
        ->setParameter('statut', $statut)
        ->getQuery()
        ->getSingleScalarResult();
}

//    /**
//     * @return Commande[] Returns an array of Commande objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Commande
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
