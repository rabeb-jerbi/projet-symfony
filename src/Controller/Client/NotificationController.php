<?php

namespace App\Controller\Client;

use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_CLIENT')]
#[Route('/client/notifications')]
class NotificationController extends AbstractController
{
    #[Route('', name: 'client_notifications', methods: ['GET'])]
    public function index(NotificationRepository $repo): Response
    {
        $user = $this->getUser();

        $notifs = $repo->findLatestForUser($user, 50);
        $unreadCount = $repo->countUnreadForUser($user);

        return $this->render('client/notification/index.html.twig', [
            'notifications' => $notifs,
            'unreadCount' => $unreadCount,
        ]);
    }

    #[Route('/mark-all', name: 'client_notifications_mark_all', methods: ['POST'])]
    public function markAll(Request $request, NotificationRepository $repo): Response
    {
        if (!$this->isCsrfTokenValid('client_notifs_mark_all', (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('client_notifications');
        }

        $repo->markAllAsRead($this->getUser());
        $this->addFlash('success', 'Toutes les notifications ont été marquées comme lues.');

        return $this->redirectToRoute('client_notifications');
    }
}
