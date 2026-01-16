<?php

namespace App\Controller\Admin;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\NotificationRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/messages')]
#[IsGranted('ROLE_ADMIN')]
class MessageController extends AbstractController
{
    #[Route('/', name: 'admin_messages', methods: ['GET'])]
    public function index(MessageRepository $repo): Response
    {
        return $this->render('admin/message/index.html.twig', [
            'threads' => $repo->findThreadsForUser($this->getUser()),
        ]);
    }

    #[Route('/{id}', name: 'admin_messages_show', methods: ['GET','POST'])]
    public function show(
        Message $root,
        Request $request,
        MessageRepository $repo,
        NotificationRepository $notifRepo,
        EntityManagerInterface $em,
        NotificationService $notifs
    ): Response {
        // root doit être un thread principal
        if ($root->getRoot() !== null) {
            $root = $root->getRoot();
        }

        // ✅ quand l’admin ouvre le thread => marquer messages reçus comme lus
        $repo->markThreadAsRead($root, $this->getUser());

        // ✅ marquer aussi les notifications liées à ce thread comme lues
        $notifRepo->markThreadNotificationsAsRead($root->getId(), $this->getUser());

        $messages = $repo->findThreadMessages($root);

        // réponse admin
        $reply = new Message();
        $form = $this->createForm(MessageType::class, $reply);
        $form->remove('subject');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reply->setSubject($root->getSubject());
            $reply->setRoot($root);
            $reply->setSender($this->getUser());

            // destinataire = client (celui qui n’est pas admin)
            $client = $root->getSender();
            if (in_array('ROLE_ADMIN', $client->getRoles(), true)) {
                $client = $root->getRecipient();
            }
            $reply->setRecipient($client);

            $em->persist($reply);

            // notif client
            $notifs->notify(
                $client,
                'Réponse admin',
                'L’admin a répondu à votre message : "'.$root->getSubject().'"',
                null,
                $reply
            );

            $em->flush();

            $this->addFlash('success', 'Réponse envoyée.');
            return $this->redirectToRoute('admin_messages_show', ['id' => $root->getId()]);
        }

        return $this->render('admin/message/show.html.twig', [
            'root' => $root,
            'messages' => $messages,
            'replyForm' => $form->createView(),
        ]);
    }
}
