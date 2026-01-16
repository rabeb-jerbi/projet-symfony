<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function countUnreadForUser(Utilisateur $user): int
    {
        // unread = messages reçus par user, pas lus, et pas envoyés par lui
        return (int) $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->andWhere('m.recipient = :u')
            ->andWhere('m.isRead = 0')
            ->setParameter('u', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function markThreadAsRead(Message $root, Utilisateur $user): void
    {
        // On marque tous les messages reçus par user dans ce thread
        $this->createQueryBuilder('m')
            ->update()
            ->set('m.isRead', ':r')
            ->andWhere('(m.root = :root OR m = :root)')
            ->andWhere('m.recipient = :u')
            ->setParameter('r', true)
            ->setParameter('root', $root)
            ->setParameter('u', $user)
            ->getQuery()
            ->execute();
    }
public function findThreadsForUser(Utilisateur $user): array
{
    // threads = messages principaux (root = null) où user est sender ou recipient
    return $this->createQueryBuilder('m')
        ->andWhere('m.root IS NULL')
        ->andWhere('m.sender = :u OR m.recipient = :u')
        ->setParameter('u', $user)
        ->orderBy('m.createdAt', 'DESC')
        ->getQuery()
        ->getResult();
}

public function findThreadMessages(Message $root): array
{
    // messages du thread = root + replies
    return $this->createQueryBuilder('m')
        ->andWhere('m = :root OR m.root = :root')
        ->setParameter('root', $root)
        ->orderBy('m.createdAt', 'ASC')
        ->getQuery()
        ->getResult();
}

    // ⚠️ tu gardes tes méthodes existantes: findThreadsForUser(), findThreadMessages()
}
