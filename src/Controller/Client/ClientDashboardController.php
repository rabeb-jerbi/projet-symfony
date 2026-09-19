<?php

namespace App\Controller\Client;

use App\Entity\Client;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/client')]
#[IsGranted('ROLE_CLIENT')]
class ClientDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'client_dashboard')]
    public function index(CommandeRepository $commandeRepository): Response
    {
        /** @var Client $user */
        $user = $this->getUser();

        if (!$user instanceof Client) {
            throw $this->createAccessDeniedException();
        }

        $commandes = $commandeRepository->findBy(
            ['client' => $user],
            ['date' => 'DESC']
        );

        return $this->render(
            'client/client_dashboard/dashboard.html.twig',
            [
                'commandes' => $commandes,
                'client' => $user,
            ]
        );
    }
}
