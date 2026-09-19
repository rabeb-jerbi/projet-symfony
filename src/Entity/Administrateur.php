<?php

namespace App\Entity;

use App\Repository\AdministrateurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdministrateurRepository::class)]
class Administrateur extends Utilisateur
{
    // Pas de constructeur, pas de propriétés supplémentaires
    // Les rôles seront définis dans les fixtures ou lors de la création
}