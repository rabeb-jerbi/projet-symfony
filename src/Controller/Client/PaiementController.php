<?php

namespace App\Controller\Client;

use App\Entity\Commande;
use App\Entity\Paiement;
use App\Form\ClientPaiementType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[IsGranted('ROLE_CLIENT')]
class PaiementController extends AbstractController
{
    #[Route('/client/commandes/{id}/payer', name: 'client_payer_commande', methods: ['GET', 'POST'])]
    public function payer(Commande $commande, Request $request, EntityManagerInterface $em): Response
    {
        // ✅ Sécurité (comparaison via email)
        $user = $this->getUser();
        if (!$user || !$commande->getClient() || !method_exists($commande->getClient(), 'getEmail')) {
            throw new AccessDeniedException();
        }

        if ($commande->getClient()->getEmail() !== $user->getUserIdentifier()) {
            throw new AccessDeniedException();
        }

        // ✅ Déjà payée
        if ($commande->getStatut() === 'VALIDEE') {
            $this->addFlash('success', 'Cette commande est déjà payée.');
            return $this->redirectToRoute('client_orders');
        }

        $paiement = new Paiement();
        $paiement->setCommande($commande);
        $paiement->setMontant((float) $commande->getPrixCmd());
        $paiement->setStatut('en_attente');
        $paiement->setDatePaiement(new \DateTimeImmutable());

        $form = $this->createForm(ClientPaiementType::class, $paiement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ✅ Si CARTE => champs obligatoires
            if ($paiement->getMethode() === 'CARTE') {
                if (!$paiement->getNomPorteur() || !$paiement->getLast4()) {
                    $this->addFlash('error', 'Veuillez remplir le nom du porteur et les 4 derniers chiffres.');

                    return $this->render('client/paiement/confirm.html.twig', [
                        'commande' => $commande,
                        'form' => $form->createView(),
                    ]);
                }
            } else {
                // Nettoyage si pas carte
                $paiement->setNomPorteur(null);
                $paiement->setLast4(null);
            }

            // ✅ Référence + simuler paiement accepté
            $paiement->setReference('PAY-' . date('YmdHis') . '-' . random_int(1000, 9999));
            $paiement->setStatut('paye');

            // ✅ Marquer commande payée
            $commande->setStatut('VALIDEE');

            $em->persist($paiement);
            $em->flush();

            $this->addFlash('success', 'Paiement confirmé avec succès.');
            return $this->redirectToRoute('client_orders');
        }

        return $this->render('client/paiement/confirm.html.twig', [
            'commande' => $commande,
            'form' => $form->createView(),
        ]);
    }
}
