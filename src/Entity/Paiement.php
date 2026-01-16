<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
class Paiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $datePaiement = null;

    #[ORM\Column(length: 30)]
    private ?string $statut = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $reference = null;

    // ✅ Méthode: CARTE / VIREMENT / ESPECES
    #[ORM\Column(length: 20)]
    private ?string $methode = null;

    // ✅ Si CARTE
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $nomPorteur = null;

    // ✅ 4 derniers chiffres
    #[ORM\Column(length: 4, nullable: true)]
    private ?string $last4 = null;

    #[ORM\ManyToOne(inversedBy: 'paiements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    public function getId(): ?int { return $this->id; }

    public function getMontant(): ?float { return $this->montant; }
    public function setMontant(float $montant): static { $this->montant = $montant; return $this; }

    public function getDatePaiement(): ?\DateTimeInterface { return $this->datePaiement; }
    public function setDatePaiement(\DateTimeInterface $datePaiement): static { $this->datePaiement = $datePaiement; return $this; }

    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $statut): static { $this->statut = $statut; return $this; }

    public function getReference(): ?string { return $this->reference; }
    public function setReference(?string $reference): static { $this->reference = $reference; return $this; }

    public function getMethode(): ?string { return $this->methode; }
    public function setMethode(string $methode): static { $this->methode = $methode; return $this; }

    public function getNomPorteur(): ?string { return $this->nomPorteur; }
    public function setNomPorteur(?string $nomPorteur): static { $this->nomPorteur = $nomPorteur; return $this; }

    public function getLast4(): ?string { return $this->last4; }
    public function setLast4(?string $last4): static { $this->last4 = $last4; return $this; }

    public function getCommande(): ?Commande { return $this->commande; }
    public function setCommande(?Commande $commande): static { $this->commande = $commande; return $this; }
}
