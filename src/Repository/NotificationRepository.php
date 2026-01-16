<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function countUnreadForUser(Utilisateur $user): int
    {
        return (int) $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->andWhere('n.recipient = :u')
            ->andWhere('n.isRead = 0')
            ->setParameter('u', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return Notification[]
     */
    public function findLatestForUser(Utilisateur $user, int $limit = 20): array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.recipient = :u')
            ->setParameter('u', $user)
            ->orderBy('n.dateCreation', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function markAllAsRead(Utilisateur $user): void
    {
        $this->createQueryBuilder('n')
            ->update()
            ->set('n.isRead', ':r')
            ->andWhere('n.recipient = :u')
            ->setParameter('r', true)
            ->setParameter('u', $user)
            ->getQuery()
            ->execute();
    }

    public function markThreadNotificationsAsRead(int $rootId, Utilisateur $user): void
    {
        $dql = "
            UPDATE App\Entity\Notification n
            SET n.isRead = true
            WHERE n.recipient = :u
              AND n.message IN (
                SELECT m.id FROM App\Entity\Message m
                WHERE (m.root = :rootId OR m.id = :rootId)
              )
        ";

        $this->getEntityManager()->createQuery($dql)
            ->setParameter('u', $user)
            ->setParameter('rootId', $rootId)
            ->execute();
    }
}
