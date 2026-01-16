<?php

namespace App\Controller\Client;

use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_CLIENT')]
#[Route('/client')]
class OrdersController extends AbstractController
{
    #[Route('/orders', name: 'client_orders', methods: ['GET'])]
    public function index(CommandeRepository $repo): Response
    {
        $orders = $repo->findBy(['client' => $this->getUser()], ['date' => 'DESC']);

        return $this->render('client/orders/index.html.twig', [
            'orders' => $orders,
        ]);
    }
}
