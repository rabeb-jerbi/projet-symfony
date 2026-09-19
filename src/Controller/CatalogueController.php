<?php

namespace App\Controller;

use App\Repository\VoitureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CatalogueController extends AbstractController
{
    #[Route('/catalogue', name: 'app_catalogue')]
    public function index(VoitureRepository $voitureRepository): Response
    {
        $voitures = $voitureRepository->findBy(['statut' => 'disponible']);

        return $this->render('catalogue/index.html.twig', [
            'voitures' => $voitures,
        ]);
    }

    #[Route('/catalogue/{id}', name: 'app_catalogue_show')]
    public function show(int $id, VoitureRepository $voitureRepository): Response
    {
        $voiture = $voitureRepository->find($id);

        if (!$voiture) {
            throw $this->createNotFoundException('Voiture non trouvée');
        }

        return $this->render('catalogue/show.html.twig', [
            'voiture' => $voiture,
        ]);
    }
}