<?php

namespace App\Entity;

use App\Repository\VoitureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoitureRepository::class)]
class Voiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $matricule = null;

    #[ORM\Column(length: 100)]
    private ?string $marque = null;

    #[ORM\Column(length: 50)]
    private ?string $modele = null;

    #[ORM\Column]
    private ?int $annee = null;

    #[ORM\Column]
    private ?int $kilometrage = null;

    #[ORM\Column]
    private ?float $prixAchat = null;

    #[ORM\Column]
    private ?float $prixLocationJour = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $documents = null;

    #[ORM\Column(length: 20)]
    private ?string $statut = null;

    // On stocke uniquement le NOM du fichier (ex: voiture1.jpg)
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    /**
     * @var Collection<int, LigneCommande>
     */
    #[ORM\OneToMany(
        targetEntity: LigneCommande::class,
        mappedBy: 'voiture',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $lignesCommande;

    /**
     * @var Collection<int, Annonce>
     */
    #[ORM\OneToMany(
        targetEntity: Annonce::class,
        mappedBy: 'voiture',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $annonces;

    public function __construct()
    {
        $this->lignesCommande = new ArrayCollection();
        $this->annonces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(string $matricule): static
    {
        $this->matricule = $matricule;
        return $this;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): static
    {
        $this->marque = $marque;
        return $this;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): static
    {
        $this->modele = $modele;
        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): static
    {
        $this->annee = $annee;
        return $this;
    }

    public function getKilometrage(): ?int
    {
        return $this->kilometrage;
    }

    public function setKilometrage(int $kilometrage): static
    {
        $this->kilometrage = $kilometrage;
        return $this;
    }

    public function getPrixAchat(): ?float
    {
        return $this->prixAchat;
    }

    public function setPrixAchat(float $prixAchat): static
    {
        $this->prixAchat = $prixAchat;
        return $this;
    }

    public function getPrixLocationJour(): ?float
    {
        return $this->prixLocationJour;
    }

    public function setPrixLocationJour(float $prixLocationJour): static
    {
        $this->prixLocationJour = $prixLocationJour;
        return $this;
    }

    public function getDocuments(): ?string
    {
        return $this->documents;
    }

    public function setDocuments(?string $documents): static
    {
        $this->documents = $documents;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;
        return $this;
    }

    /**
     * @return Collection<int, LigneCommande>
     */
    public function getLignesCommande(): Collection
    {
        return $this->lignesCommande;
    }

    public function addLigneCommande(LigneCommande $ligneCommande): static
    {
        if (!$this->lignesCommande->contains($ligneCommande)) {
            $this->lignesCommande->add($ligneCommande);
            $ligneCommande->setVoiture($this);
        }
        return $this;
    }

    public function removeLigneCommande(LigneCommande $ligneCommande): static
    {
        if ($this->lignesCommande->removeElement($ligneCommande)) {
            if ($ligneCommande->getVoiture() === $this) {
                $ligneCommande->setVoiture(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Annonce>
     */
    public function getAnnonces(): Collection
    {
        return $this->annonces;
    }

    public function addAnnonce(Annonce $annonce): static
    {
        if (!$this->annonces->contains($annonce)) {
            $this->annonces->add($annonce);
            $annonce->setVoiture($this);
        }
        return $this;
    }

    public function removeAnnonce(Annonce $annonce): static
    {
        if ($this->annonces->removeElement($annonce)) {
            if ($annonce->getVoiture() === $this) {
                $annonce->setVoiture(null);
            }
        }
        return $this;
    }
}
