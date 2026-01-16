<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\Utilisateur;
use App\Entity\Commande;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function notify(
        Utilisateur $recipient,
        string $title,
        string $contenu,
        ?Commande $commande = null,
        ?Message $message = null
    ): void {
        $n = (new Notification())
            ->setRecipient($recipient)
            ->setTitle($title)
            ->setContenu($contenu)
            ->setCommande($commande)
            ->setMessage($message);

        $this->em->persist($n);
        $this->em->flush();
    }
}
