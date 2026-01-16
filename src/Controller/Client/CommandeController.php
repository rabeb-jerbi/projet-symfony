<?php

namespace App\Controller\Client;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Voiture;
use App\Form\LocationType;
use App\Repository\UtilisateurRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_CLIENT')]
#[Route('/client/commande')]
class CommandeController extends AbstractController
{
    #[Route('/acheter/{id}', name: 'client_commande_achat', methods: ['POST'])]
    public function achat(
        Voiture $voiture,
        EntityManagerInterface $em,
        UtilisateurRepository $userRepo,
        NotificationService $notifs
    ): Response {
        $commande = new Commande();
        $commande->setClient($this->getUser());
        $commande->setType('ACHAT');
        $commande->setStatut('EN_ATTENTE');
        $commande->setDate(new \DateTimeImmutable());
        $commande->setPrixCmd((float) $voiture->getPrixAchat());

        // 1 ligne (optionnel) : pour achat tu peux aussi créer une ligneCommande sans dates
        $ligne = new LigneCommande();
        $ligne->setNumLigne(1);
        $ligne->setVoiture($voiture);
        $ligne->setCommande($commande);
        $ligne->setMontantParJour(0);
        $ligne->setTotaleTTC((float) $voiture->getPrixAchat());

        $commande->addLignesCommande($ligne);

        $em->persist($commande);
        $em->flush();

        // notif admin
        $admin = $userRepo->findOneByRole('ROLE_ADMIN');
        if ($admin) {
            $notifs->notify(
                $admin,
                'Nouvelle commande (Achat)',
                'Une nouvelle commande d’achat a été créée (Commande #' . $commande->getId() . ').',
                $commande,
                null
            );
        }

        $this->addFlash('success', 'Commande d’achat créée avec succès.');
        return $this->redirectToRoute('client_orders');
    }

    #[Route('/louer/{id}', name: 'client_commande_location', methods: ['GET', 'POST'])]
    public function location(
        Voiture $voiture,
        Request $request,
        EntityManagerInterface $em,
        UtilisateurRepository $userRepo,
        NotificationService $notifs
    ): Response {
        $form = $this->createForm(LocationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \DateTimeInterface $dateDebut */
            $dateDebut = $form->get('dateDebut')->getData();
            /** @var \DateTimeInterface $dateFin */
            $dateFin = $form->get('dateFin')->getData();

            if (!$dateDebut || !$dateFin || $dateFin < $dateDebut) {
                $this->addFlash('error', 'Période invalide (date fin doit être >= date début).');
                return $this->redirectToRoute('client_commande_location', ['id' => $voiture->getId()]);
            }

            $days = (int) $dateDebut->diff($dateFin)->days;
            $days = max(1, $days + 1); // inclusif

            $prixJour = (float) $voiture->getPrixLocationJour();
            $total = $prixJour * $days;

            $commande = new Commande();
            $commande->setClient($this->getUser());
            $commande->setType('LOCATION');
            $commande->setStatut('EN_ATTENTE');
            $commande->setDate(new \DateTimeImmutable());
            $commande->setPrixCmd($total);

            $ligne = new LigneCommande();
            $ligne->setNumLigne(1);
            $ligne->setCommande($commande);
            $ligne->setVoiture($voiture);
            $ligne->setDateDebut($dateDebut);
            $ligne->setDateFin($dateFin);
            $ligne->setMontantParJour($prixJour);
            $ligne->setTotaleTTC($total);

            $commande->addLignesCommande($ligne);

            $em->persist($commande);
            $em->flush();

            // notif admin
            $admin = $userRepo->findOneByRole('ROLE_ADMIN');
            if ($admin) {
                $notifs->notify(
                    $admin,
                    'Nouvelle commande (Location)',
                    'Une nouvelle commande de location a été créée (Commande #' . $commande->getId() . ').',
                    $commande,
                    null
                );
            }

            $this->addFlash('success', 'Commande de location créée avec succès.');
            return $this->redirectToRoute('client_orders');
        }

        return $this->render('client/orders/location.html.twig', [
            'voiture' => $voiture,
            'form' => $form->createView(),
        ]);
    }
}
