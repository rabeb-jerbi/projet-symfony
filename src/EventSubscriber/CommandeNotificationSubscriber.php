<?php

namespace App\EventSubscriber;

use App\Entity\Commande;
use App\Entity\Notification;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class CommandeNotificationSubscriber implements EventSubscriber
{
    public function __construct(private UtilisateurRepository $userRepo) {}

    public function getSubscribedEvents(): array
    {
        return [Events::postPersist];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // On réagit فقط à la création d'une commande
        if (!$entity instanceof Commande) {
            return;
        }

        $em = $args->getObjectManager();

        // 1er admin trouvé (comme ton code)
        $admin = $this->userRepo->findOneByRole('ROLE_ADMIN');
        if (!$admin) {
            return;
        }

        // créer notif (⚠️ pas de flush ici, Doctrine va flush déjà)
        $notif = (new Notification())
            ->setRecipient($admin)
            ->setTitle('Nouvelle commande')
            ->setContenu('Une nouvelle commande a été créée (Commande #' . $entity->getId() . ').')
            ->setCommande($entity);

        $em->persist($notif);
        // pas de flush
    }
}
