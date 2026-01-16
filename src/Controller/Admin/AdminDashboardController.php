<?php

namespace App\Controller\Admin;

use App\Repository\ClientRepository;
use App\Repository\CommandeRepository;
use App\Repository\VoitureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'admin_dashboard')]
    public function index(
        VoitureRepository $voitureRepo,
        ClientRepository $clientRepo,
        CommandeRepository $commandeRepo
    ): Response {
        $totalVoitures = $voitureRepo->count([]);
        $totalClients = $clientRepo->count([]);
        $totalCommandes = $commandeRepo->count([]);

        $revenuTotal = 0.0;
        foreach ($commandeRepo->findAll() as $commande) {
            $revenuTotal += (float) $commande->getPrixCmd();
        }

        $dernieresCommandes = $commandeRepo->findBy([], ['date' => 'DESC'], 10);
        $derniersClients = $clientRepo->findBy([], ['id' => 'DESC'], 5);

        $voituresDisponibles = $voitureRepo->count(['statut' => 'disponible']);
        $voituresLouees = $voitureRepo->count(['statut' => 'loué']);
        $voituresVendues = $voitureRepo->count(['statut' => 'vendu']);

        return $this->render('admin/dashboard/index.html.twig', [
            'totalVoitures' => $totalVoitures,
            'totalClients' => $totalClients,
            'totalCommandes' => $totalCommandes,
            'revenuTotal' => $revenuTotal,
            'dernieresCommandes' => $dernieresCommandes,
            'derniersClients' => $derniersClients,
            'voituresDisponibles' => $voituresDisponibles,
            'voituresLouees' => $voituresLouees,
            'voituresVendues' => $voituresVendues,
        ]);
    }
}
