<?php

namespace App\Controller;

use App\Repository\VoitureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CatalogueController extends AbstractController
{
    #[Route('/catalogue', name: 'app_catalogue')]
    public function index(Request $request, VoitureRepository $voitureRepository): Response
    {
        $searchQuery = (string) $request->query->get('q', '');
        $typeFilter  = (string) $request->query->get('type', '');
        $sortBy      = (string) $request->query->get('sort', 'newest');

        /**
         * IMPORTANT:
         * - Si ton repository filtre vraiment "type", ça marche direct.
         * - Si tu n'as pas de champ type en DB, ton repository doit IGNORER ce filtre.
         */
        $voitures = $voitureRepository->findBySearchCriteria([
            'search' => $searchQuery,
            'type'   => $typeFilter,
            'sort'   => $sortBy,
            'statut' => 'disponible',
        ]);

        return $this->render('catalogue/index.html.twig', [
            'voitures' => $voitures,
            'searchQuery' => $searchQuery,
            'typeFilter' => $typeFilter,
            'sortBy' => $sortBy,
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
