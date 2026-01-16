<?php

namespace App\Controller\Client;

use App\Entity\Client;
use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Voiture;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/client')]
#[IsGranted('ROLE_CLIENT')]
class ClientDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'client_dashboard')]
    public function index(CommandeRepository $commandeRepository): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Client) {
            throw $this->createAccessDeniedException();
        }

        $commandes = $commandeRepository->findBy(['client' => $user], ['date' => 'DESC']);

        return $this->render('client/client_dashboard/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/commande/new/{id}/{type}', name: 'app_commande_client_new')]
    public function newCommande(
        Voiture $voiture,
        string $type,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof Client) {
            throw $this->createAccessDeniedException();
        }

        // (optionnel) Sécuriser le paramètre type
        if (!in_array($type, ['achat', 'location'], true)) {
            throw $this->createNotFoundException('Type de commande invalide.');
        }

        $commande = new Commande();
        $commande->setClient($user);
        $commande->setType($type);
        $commande->setDate(new \DateTimeImmutable());
        $commande->setStatut('en attente');

        $ligne = new LigneCommande();
        $ligne->setVoiture($voiture);
        $ligne->setNumLigne(1);

        if ($type === 'achat') {
            $prix = (float) $voiture->getPrixAchat();
            $commande->setPrixCmd($prix);

            $ligne->setMontantParJour(0);
            $ligne->setTotaleTTC($prix);
        } else {
            $prixJour = (float) $voiture->getPrixLocationJour();
            $commande->setPrixCmd($prixJour);

            $ligne->setMontantParJour($prixJour);
            $ligne->setTotaleTTC($prixJour);

            $ligne->setDateDebut(new \DateTimeImmutable());
            $ligne->setDateFin((new \DateTimeImmutable())->modify('+1 day'));
        }

        // IMPORTANT: addLignesCommande() définit automatiquement $ligne->setCommande($commande)
        $commande->addLignesCommande($ligne);

        // ✅ plus besoin de persist($ligne) car cascade persist est activé dans Commande
        $entityManager->persist($commande);
        $entityManager->flush();

        $this->addFlash('success', 'Votre commande a été enregistrée avec succès !');

        return $this->redirectToRoute('client_dashboard');
    }
}
