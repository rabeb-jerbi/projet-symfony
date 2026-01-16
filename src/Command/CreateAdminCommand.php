<?php

namespace App\Command;

use App\Entity\Administrateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Créer un compte administrateur',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Rechercher si un utilisateur avec cet email existe (Client ou Admin)
        $existingUser = $this->entityManager->getRepository('App\Entity\Utilisateur')
            ->findOneBy(['email' => 'admin@yourcar.tn']);

        if ($existingUser) {
            $io->warning('Un utilisateur avec cet email existe déjà (' . get_class($existingUser) . '). Suppression en cours...');
            $this->entityManager->remove($existingUser);
            $this->entityManager->flush();
            $io->success('Ancien utilisateur supprimé !');
        }

        // Créer l'administrateur
        $admin = new Administrateur();
        $admin->setEmail('admin@yourcar.tn');
        $admin->setNom('Administrateur Principal');
        $admin->setRoles(['ROLE_ADMIN']); // UNIQUEMENT ROLE_ADMIN, pas ROLE_CLIENT
        
        // Hasher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($admin, 'admin123');
        $admin->setPassword($hashedPassword);

        // Persister
        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $io->success('Compte administrateur créé avec succès !');
        $io->table(
            ['Champ', 'Valeur'],
            [
                ['Email', 'admin@yourcar.tn'],
                ['Mot de passe', 'admin123'],
                ['Rôles', 'ROLE_ADMIN'],
                ['Type', 'Administrateur (pas Client)'],
            ]
        );

        return Command::SUCCESS;
    }
}
