<?php

namespace App\Controller\Client;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Paiement;
use App\Entity\Voiture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/client')]
#[IsGranted('ROLE_CLIENT')]
class ClientOrderController extends AbstractController
{
    #[Route('/acheter/{id}', name: 'client_acheter', methods: ['POST'])]
    public function acheter(Voiture $voiture, EntityManagerInterface $em, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('buy'.$voiture->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        if ($voiture->getStatut() !== 'disponible') {
            $this->addFlash('danger', 'Cette voiture n’est pas disponible.');
            return $this->redirectToRoute('app_catalogue_show', ['id' => $voiture->getId()]);
        }

        /** @var \App\Entity\Client $client */
        $client = $this->getUser();

        $commande = new Commande();
        $commande->setClient($client);
        $commande->setType('achat');
        $commande->setDate(new \DateTime());
        $commande->setStatut('en_attente');
        $commande->setPrixCmd($voiture->getPrixAchat());

        $ligne = new LigneCommande();
        $ligne->setCommande($commande);
        $ligne->setVoiture($voiture);
        $ligne->setNumLigne(1);
        $ligne->setMontantParJour(0);
        $ligne->setTotaleTTC($voiture->getPrixAchat());

        $voiture->setStatut('reservee');

        $em->persist($commande);
        $em->persist($ligne);
        $em->flush();

        return $this->redirectToRoute('client_payer', ['id' => $commande->getId()]);
    }

    #[Route('/louer/{id}', name: 'client_louer', methods: ['POST'])]
    public function louer(Voiture $voiture, EntityManagerInterface $em, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('rent'.$voiture->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        if ($voiture->getStatut() !== 'disponible') {
            $this->addFlash('danger', 'Cette voiture n’est pas disponible.');
            return $this->redirectToRoute('app_catalogue_show', ['id' => $voiture->getId()]);
        }

        $dateDebutStr = $request->request->get('dateDebut');
        $dateFinStr = $request->request->get('dateFin');

        if (!$dateDebutStr || !$dateFinStr) {
            $this->addFlash('danger', 'Veuillez choisir une date de début et de fin.');
            return $this->redirectToRoute('app_catalogue_show', ['id' => $voiture->getId()]);
        }

        $dateDebut = new \DateTime($dateDebutStr);
        $dateFin = new \DateTime($dateFinStr);

        if ($dateFin <= $dateDebut) {
            $this->addFlash('danger', 'La date de fin doit être après la date de début.');
            return $this->redirectToRoute('app_catalogue_show', ['id' => $voiture->getId()]);
        }

        $jours = (int) $dateDebut->diff($dateFin)->days;
        $jours = max($jours, 1);

        $total = $jours * $voiture->getPrixLocationJour();

        /** @var \App\Entity\Client $client */
        $client = $this->getUser();

        $commande = new Commande();
        $commande->setClient($client);
        $commande->setType('location');
        $commande->setDate(new \DateTime());
        $commande->setStatut('en_attente');
        $commande->setPrixCmd($total);

        $ligne = new LigneCommande();
        $ligne->setCommande($commande);
        $ligne->setVoiture($voiture);
        $ligne->setNumLigne(1);
        $ligne->setDateDebut($dateDebut);
        $ligne->setDateFin($dateFin);
        $ligne->setMontantParJour($voiture->getPrixLocationJour());
        $ligne->setTotaleTTC($total);

        $voiture->setStatut('reservee');

        $em->persist($commande);
        $em->persist($ligne);
        $em->flush();

        return $this->redirectToRoute('client_payer', ['id' => $commande->getId()]);
    }

    #[Route('/payer/{id}', name: 'client_payer', methods: ['GET', 'POST'])]
    public function payer(Commande $commande, EntityManagerInterface $em, Request $request): Response
    {
        /** @var \App\Entity\Client $client */
        $client = $this->getUser();

        if ($commande->getClient()->getId() !== $client->getId()) {
            throw $this->createAccessDeniedException();
        }

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('pay'.$commande->getId(), $request->request->get('_token'))) {
                throw $this->createAccessDeniedException();
            }

            $paiement = new Paiement();
            $paiement->setMontant($commande->getPrixCmd());
            $paiement->setDatePaiement(new \DateTime());
            $paiement->setStatut('valide');
            $paiement->setRealtion($commande);

            $commande->setStatut('payee');

            // Met à jour le statut voiture
            foreach ($commande->getLignesCommande() as $ligne) {
                $voiture = $ligne->getVoiture();
                $voiture->setStatut($commande->getType() === 'achat' ? 'vendue' : 'loue');
            }

            $em->persist($paiement);
            $em->flush();

            $this->addFlash('success', 'Paiement effectué avec succès ✅');
            return $this->redirectToRoute('client_dashboard');
        }

        return $this->render('client/payer.html.twig', [
            'commande' => $commande,
        ]);
    }
}
