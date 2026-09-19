<?php

namespace App\Controller\Admin;

use App\Repository\VoitureRepository;
use App\Repository\CommandeRepository;
use App\Repository\ClientRepository;
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
        CommandeRepository $commandeRepo,
        ClientRepository $clientRepo
    ): Response {
        // Statistiques
        $totalVoitures = $voitureRepo->count([]);
        $voituresDisponibles = $voitureRepo->count(['statut' => 'disponible']);
        $totalCommandes = $commandeRepo->count([]);
        $totalClients = $clientRepo->count([]);

        return $this->render('admin/dashboard.html.twig', [
            'totalVoitures' => $totalVoitures,
            'voituresDisponibles' => $voituresDisponibles,
            'totalCommandes' => $totalCommandes,
            'totalClients' => $totalClients,
        ]);
    }
}