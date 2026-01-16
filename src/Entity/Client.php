<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client extends Utilisateur
{
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire.")]
    private ?string $adresse = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Le numéro de téléphone est obligatoire.")]
    #[Assert\Regex(pattern: "/^[0-9]{8,}$/", message: "Le numéro de téléphone doit contenir au moins 8 chiffres.")]
    private ?string $numTeleph = null;

    /** @var Collection<int, Commande> */
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Commande::class, orphanRemoval: true)]
    private Collection $commandes;

    /** @var Collection<int, Avis> */
    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Avis::class, orphanRemoval: true)]
    private Collection $avis;

    public function __construct()
    {
        parent::__construct(); // important: initialise notifications dans Utilisateur
        $this->setRoles(['ROLE_CLIENT']);
        $this->commandes = new ArrayCollection();
        $this->avis = new ArrayCollection();
    }

    public function getAdresse(): ?string { return $this->adresse; }
    public function setAdresse(string $adresse): static { $this->adresse = $adresse; return $this; }

    public function getNumTeleph(): ?string { return $this->numTeleph; }
    public function setNumTeleph(string $numTeleph): static { $this->numTeleph = $numTeleph; return $this; }

    /** @return Collection<int, Commande> */
    public function getCommandes(): Collection { return $this->commandes; }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setClient($this);
        }
        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            if ($commande->getClient() === $this) {
                $commande->setClient(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, Avis> */
    public function getAvis(): Collection { return $this->avis; }

    public function addAvis(Avis $avis): static
    {
        if (!$this->avis->contains($avis)) {
            $this->avis->add($avis);
            $avis->setClient($this);
        }
        return $this;
    }

    public function removeAvis(Avis $avis): static
    {
        if ($this->avis->removeElement($avis)) {
            if ($avis->getClient() === $this) {
                $avis->setClient(null);
            }
        }
        return $this;
    }
}
