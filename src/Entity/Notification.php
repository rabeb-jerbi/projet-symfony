<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $recipient = null;

    #[ORM\Column(length: 150)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenu = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $dateCreation = null;

    #[ORM\Column(options: ['default' => 0])]
    private bool $isRead = false;

    #[ORM\ManyToOne]
    private ?Commande $commande = null;

    #[ORM\ManyToOne]
    private ?Message $message = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getRecipient(): ?Utilisateur { return $this->recipient; }
    public function setRecipient(?Utilisateur $recipient): static { $this->recipient = $recipient; return $this; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): static { $this->title = $title; return $this; }

    public function getContenu(): ?string { return $this->contenu; }
    public function setContenu(string $contenu): static { $this->contenu = $contenu; return $this; }

    public function getDateCreation(): ?\DateTimeImmutable { return $this->dateCreation; }
    public function setDateCreation(\DateTimeImmutable $dateCreation): static { $this->dateCreation = $dateCreation; return $this; }

    public function isRead(): bool { return $this->isRead; }
    public function setIsRead(bool $isRead): static { $this->isRead = $isRead; return $this; }

    public function getCommande(): ?Commande { return $this->commande; }
    public function setCommande(?Commande $commande): static { $this->commande = $commande; return $this; }

    public function getMessage(): ?Message { return $this->message; }
    public function setMessage(?Message $message): static { $this->message = $message; return $this; }
}
