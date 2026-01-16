<?php

namespace App\Controller;

use App\Repository\VoitureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(VoitureRepository $voitureRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_dashboard');
        }

        $featuredVoitures = $voitureRepository->findBy(
            ['statut' => 'disponible'],
            ['id' => 'DESC'],
            6
        );

        return $this->render('home/index.html.twig', [
            'voitures' => $featuredVoitures,
        ]);
    }
}
