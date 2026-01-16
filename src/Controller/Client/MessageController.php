<?php

namespace App\Controller\Client;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\UtilisateurRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/client/messages')]
#[IsGranted('ROLE_CLIENT')]
class MessageController extends AbstractController
{
    #[Route('/', name: 'client_messages', methods: ['GET'])]
    public function index(MessageRepository $repo): Response
    {
        return $this->render('client/messages/index.html.twig', [
            'threads' => $repo->findThreadsForUser($this->getUser()),
        ]);
    }

    #[Route('/new', name: 'client_messages_new', methods: ['GET','POST'])]
    public function new(
        Request $request,
        UtilisateurRepository $userRepo,
        EntityManagerInterface $em,
        NotificationService $notifs
    ): Response {
        $msg = new Message();
        $form = $this->createForm(MessageType::class, $msg);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ✅ on envoie au 1er admin trouvé
            $admin = $userRepo->findOneByRole('ROLE_ADMIN'); // on va ajouter la méthode dans repository (plus bas)
            if (!$admin) {
                $this->addFlash('error', "Aucun admin trouvé.");
                return $this->redirectToRoute('client_messages');
            }

            $msg->setSender($this->getUser());
            $msg->setRecipient($admin);

            $em->persist($msg);

            // notif admin
            $notifs->notify(
                $admin,
                'Nouveau message client',
                'Un client vous a envoyé un message : "'.$msg->getSubject().'"',
                null,
                $msg
            );

            $em->flush();

            $this->addFlash('success', 'Message envoyé.');
            return $this->redirectToRoute('client_messages');
        }

        return $this->render('client/messages/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'client_messages_show', methods: ['GET','POST'])]
    public function show(
        Message $root,
        Request $request,
        MessageRepository $repo,
        EntityManagerInterface $em,
        NotificationService $notifs
    ): Response {
        // root doit être un thread principal
        if ($root->getRoot() !== null) {
            $root = $root->getRoot();
        }

        $messages = $repo->findThreadMessages($root);

        // réponse (client)
        $reply = new Message();
        $form = $this->createForm(MessageType::class, $reply);
        $form->remove('subject'); // pas besoin de sujet sur reply
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reply->setSubject($root->getSubject());
            $reply->setRoot($root);
            $reply->setSender($this->getUser());
            $reply->setRecipient($root->getSender()->getRoles()[0] === 'ROLE_ADMIN' ? $root->getSender() : $root->getRecipient());

            $em->persist($reply);

            // notif admin
            $notifs->notify(
                $reply->getRecipient(),
                'Réponse client',
                'Le client a répondu : "'.$root->getSubject().'"',
                null,
                $reply
            );

            $em->flush();
            return $this->redirectToRoute('client_messages_show', ['id' => $root->getId()]);
        }

        return $this->render('client/messages/show.html.twig', [
            'root' => $root,
            'messages' => $messages,
            'replyForm' => $form->createView(),
        ]);
    }
}
