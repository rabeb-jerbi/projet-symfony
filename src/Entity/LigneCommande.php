<?php

namespace App\Entity;

use App\Repository\LigneCommandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneCommandeRepository::class)]
class LigneCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $numLigne = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateDebut = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateFin = null;

    #[ORM\Column]
    private ?float $montantParJour = null;

    #[ORM\Column]
    private ?float $totaleTTC = null;

    #[ORM\ManyToOne(inversedBy: 'lignesCommande')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    #[ORM\ManyToOne(inversedBy: 'lignesCommande')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Voiture $Voiture = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumLigne(): ?int
    {
        return $this->numLigne;
    }

    public function setNumLigne(int $numLigne): static
    {
        $this->numLigne = $numLigne;

        return $this;
    }

    public function getDateDebut(): ?\DateTime
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTime $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTime $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getMontantParJour(): ?float
    {
        return $this->montantParJour;
    }

    public function setMontantParJour(float $montantParJour): static
    {
        $this->montantParJour = $montantParJour;

        return $this;
    }

    public function getTotaleTTC(): ?float
    {
        return $this->totaleTTC;
    }

    public function setTotaleTTC(float $totaleTTC): static
    {
        $this->totaleTTC = $totaleTTC;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): static
    {
        $this->commande = $commande;

        return $this;
    }

    public function getVoiture(): ?Voiture
    {
        return $this->Voiture;
    }

    public function setVoiture(?Voiture $Voiture): static
    {
        $this->Voiture = $Voiture;

        return $this;
    }
}
