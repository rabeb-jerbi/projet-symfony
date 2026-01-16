<?php

namespace App\Controller\Client;

use App\Entity\Client;
use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Voiture;
use App\Form\LigneCommandeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_CLIENT')]
class LocationController extends AbstractController
{
    #[Route('/client/location/{id}', name: 'client_location', methods: ['GET','POST'])]
    public function louer(Voiture $voiture, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Client) {
            throw new AccessDeniedException();
        }

        $ligne = new LigneCommande();
        $form = $this->createForm(LigneCommandeType::class, $ligne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $debut = $ligne->getDateDebut();
            $fin   = $ligne->getDateFin();

            if (!$debut || !$fin || $fin < $debut) {
                $this->addFlash('error', 'Dates invalides.');
                return $this->redirectToRoute('client_location', ['id' => $voiture->getId()]);
            }

            // ✅ nb jours (min 1)
            $days = (int) $debut->diff($fin)->days;
            $days = max($days, 1);

            $montantJour = (float) $voiture->getPrixLocationJour();
            $total = $days * $montantJour;

            // ✅ création commande
            $commande = new Commande();
            $commande->setClient($user);
            $commande->setType('LOCATION');
            $commande->setStatut('EN_ATTENTE');
            $commande->setDate(new \DateTimeImmutable());
            $commande->setPrixCmd($total);

            // ✅ ligne commande
            $ligne->setCommande($commande);
            $ligne->setVoiture($voiture);
            $ligne->setNumLigne(1);
            $ligne->setMontantParJour($montantJour);
            $ligne->setTotaleTTC($total);

            $em->persist($commande);
            $em->persist($ligne);
            $em->flush();

            $this->addFlash('success', 'Location créée. Vous pouvez maintenant payer.');
            return $this->redirectToRoute('client_orders');
        }

        return $this->render('client/location/new.html.twig', [
            'voiture' => $voiture,
            'form' => $form->createView(),
        ]);
    }
}
