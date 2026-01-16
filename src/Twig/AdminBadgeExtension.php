<?php

namespace App\Twig;

use App\Repository\MessageRepository;
use App\Repository\NotificationRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdminBadgeExtension extends AbstractExtension
{
    public function __construct(
        private Security $security,
        private MessageRepository $messageRepo,
        private NotificationRepository $notifRepo
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('admin_unread_messages', [$this, 'unreadMessages']),
            new TwigFunction('admin_unread_notifications', [$this, 'unreadNotifications']),
        ];
    }

    public function unreadMessages(): int
    {
        $user = $this->security->getUser();
        if (!$user) return 0;
        return $this->messageRepo->countUnreadForUser($user);
    }

    public function unreadNotifications(): int
    {
        $user = $this->security->getUser();
        if (!$user) return 0;
        return $this->notifRepo->countUnreadForUser($user);
    }
}
