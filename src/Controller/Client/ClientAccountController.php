<?php

namespace App\Controller\Client;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/client')]
#[IsGranted('ROLE_CLIENT')]
class ClientAccountController extends AbstractController
{
    #[Route('/profile', name: 'client_profile', methods: ['GET'])]
    public function profile(): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Client) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('client/profile/show.html.twig', [
            'client' => $user,
        ]);
    }

    #[Route('/profile/edit', name: 'client_profile_edit', methods: ['GET', 'POST'])]
    public function editProfile(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Client) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ClientType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Profil mis à jour avec succès.');
            return $this->redirectToRoute('client_profile');
        }

        return $this->render('client/profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/orders', name: 'client_orders', methods: ['GET'])]
    public function orders(CommandeRepository $commandeRepository): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Client) {
            throw $this->createAccessDeniedException();
        }

        $commandes = $commandeRepository->findBy(['client' => $user], ['date' => 'DESC']);

        return $this->render('client/orders/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }
}
