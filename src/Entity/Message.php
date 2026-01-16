<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $subject = null;

    #[ORM\Column(type: 'text')]
    private ?string $content = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private bool $isRead = false;

    // expéditeur (Client ou Admin)
    #[ORM\ManyToOne(inversedBy: 'sentMessages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $sender = null;

    // destinataire (Client ou Admin)
    #[ORM\ManyToOne(inversedBy: 'receivedMessages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $recipient = null;

    // root de conversation (null = message principal)
    #[ORM\ManyToOne(targetEntity: Message::class)]
    private ?Message $root = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->isRead = false;
    }

    public function getId(): ?int { return $this->id; }

    public function getSubject(): ?string { return $this->subject; }
    public function setSubject(string $subject): static { $this->subject = $subject; return $this; }

    public function getContent(): ?string { return $this->content; }
    public function setContent(string $content): static { $this->content = $content; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }

    public function isRead(): bool { return $this->isRead; }
    public function setIsRead(bool $isRead): static { $this->isRead = $isRead; return $this; }

    public function getSender(): ?Utilisateur { return $this->sender; }
    public function setSender(?Utilisateur $sender): static { $this->sender = $sender; return $this; }

    public function getRecipient(): ?Utilisateur { return $this->recipient; }
    public function setRecipient(?Utilisateur $recipient): static { $this->recipient = $recipient; return $this; }

    public function getRoot(): ?Message { return $this->root; }
    public function setRoot(?Message $root): static { $this->root = $root; return $this; }
}
