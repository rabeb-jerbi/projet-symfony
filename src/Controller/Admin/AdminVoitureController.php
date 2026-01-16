<?php

namespace App\Controller\Admin;

use App\Entity\Voiture;
use App\Form\VoitureType;
use App\Repository\VoitureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/voiture')]
#[IsGranted('ROLE_ADMIN')]
class AdminVoitureController extends AbstractController
{
    #[Route('/', name: 'admin_voitures', methods: ['GET'])]
    public function index(VoitureRepository $voitureRepository): Response
    {
        return $this->render('admin/voiture/index.html.twig', [
            'voitures' => $voitureRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_voiture_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $voiture = new Voiture();
        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // upload photo
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $safeName = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $safeName);
                $newFilename = $safeName.'-'.uniqid().'.'.$photoFile->guessExtension();

                try {
                    $photoFile->move($this->getParameter('voitures_upload_dir'), $newFilename);
                    $voiture->setPhoto($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', "Erreur upload image.");
                }
            }

            $entityManager->persist($voiture);
            $entityManager->flush();

            $this->addFlash('success', 'Véhicule créé avec succès !');
            return $this->redirectToRoute('admin_voitures');
        }

        return $this->render('admin/voiture/form.html.twig', [
            'form' => $form->createView(),
            'voiture' => $voiture,
            'isEdit' => false,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_voiture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Voiture $voiture, EntityManagerInterface $entityManager): Response
    {
        $oldPhoto = $voiture->getPhoto();

        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $safeName = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $safeName);
                $newFilename = $safeName.'-'.uniqid().'.'.$photoFile->guessExtension();

                try {
                    $photoFile->move($this->getParameter('voitures_upload_dir'), $newFilename);
                    $voiture->setPhoto($newFilename);
                } catch (FileException $e) {
                    $voiture->setPhoto($oldPhoto);
                    $this->addFlash('danger', "Erreur upload image.");
                }
            } else {
                // si pas de nouvelle photo, on garde l’ancienne
                $voiture->setPhoto($oldPhoto);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Véhicule modifié avec succès !');
            return $this->redirectToRoute('admin_voitures');
        }

        return $this->render('admin/voiture/form.html.twig', [
            'form' => $form->createView(),
            'voiture' => $voiture,
            'isEdit' => true,
        ]);
    }

    #[Route('/{id}', name: 'admin_voiture_delete', methods: ['POST'])]
    public function delete(Request $request, Voiture $voiture, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$voiture->getId(), $request->request->get('_token'))) {
            $entityManager->remove($voiture);
            $entityManager->flush();
            $this->addFlash('success', 'Véhicule supprimé avec succès !');
        }

        return $this->redirectToRoute('admin_voitures');
    }
}
