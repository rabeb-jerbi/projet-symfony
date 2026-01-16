<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/commandes')]
#[IsGranted('ROLE_ADMIN')]
class AdminCommandeController extends AbstractController
{
    #[Route('', name: 'admin_commandes', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('admin/commande/index.html.twig', [
            'commandes' => $commandeRepository->findBy([], ['date' => 'DESC']),
        ]);
    }

    #[Route('/{id}/status', name: 'admin_commande_status', methods: ['POST'])]
    public function updateStatus(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $newStatus = (string) $request->request->get('statut', '');

        // ✅ statuts normalisés (sans accents)
        $allowed = ['EN_ATTENTE', 'VALIDEE', 'LIVREE', 'ANNULEE'];

        if (in_array($newStatus, $allowed, true)) {
            $commande->setStatut($newStatus);
            $entityManager->flush();
            $this->addFlash('success', 'Statut de la commande mis à jour !');
        } else {
            $this->addFlash('danger', 'Statut invalide.');
        }

        return $this->redirectToRoute('admin_commandes');
    }

    #[Route('/{id}', name: 'admin_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
            $this->addFlash('success', 'Commande supprimée avec succès !');
        }

        return $this->redirectToRoute('admin_commandes');
    }
}
