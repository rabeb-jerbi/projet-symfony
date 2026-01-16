<?php

namespace App\Controller\Admin;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/utilisateur')]
#[IsGranted('ROLE_ADMIN')]
class AdminUtilisateurController extends AbstractController
{
    #[Route('s', name: 'admin_utilisateurs', methods: ['GET'])]
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        return $this->render('admin/utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'admin_utilisateur_delete', methods: ['POST'])]
    public function delete(Request $request, int $id, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = $utilisateurRepository->find($id);
        
        if($utilisateur && $this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($utilisateur);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur supprimé avec succès !');
        }

        return $this->redirectToRoute('admin_utilisateurs');
    }
}
