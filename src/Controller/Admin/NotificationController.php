<?php

namespace App\Controller\Admin;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/notifications')]
#[IsGranted('ROLE_ADMIN')]
class NotificationController extends AbstractController
{
    #[Route('/', name: 'admin_notifications', methods: ['GET'])]
    public function index(NotificationRepository $repo): Response
    {
        $user = $this->getUser();

        return $this->render('admin/notification/index.html.twig', [ // ✅ SINGULIER
            'notifications' => $repo->findLatestForUser($user, 30),
            'unreadCount'   => $repo->countUnreadForUser($user),
        ]);
    }

    #[Route('/mark-all', name: 'admin_notifications_mark_all', methods: ['POST'])]
    public function readAll(NotificationRepository $repo): Response
    {
        $repo->markAllAsRead($this->getUser());
        $this->addFlash('success', 'Toutes les notifications ont été marquées comme lues.');
        return $this->redirectToRoute('admin_notifications');
    }

    #[Route('/mark-all', name: 'admin_notifications_mark_all', methods: ['POST'])]
    public function readOne(
        Notification $notif,
        NotificationRepository $repo,
        EntityManagerInterface $em,
        Request $request
    ): Response {
        $repo->markOneAsRead($notif, $this->getUser());

        // ✅ Redirection intelligente
        if ($notif->getCommande()) {
            return $this->redirectToRoute('admin_commandes');
        }

        if ($notif->getMessage()) {
            $root = $notif->getMessage()->getRoot() ?? $notif->getMessage();
            return $this->redirectToRoute('admin_messages_show', ['id' => $root->getId()]);
        }

        return $this->redirectToRoute('admin_notifications');
    }
}
