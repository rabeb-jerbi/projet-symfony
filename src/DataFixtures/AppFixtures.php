<?php

namespace App\DataFixtures;

use App\Entity\Administrateur;
use App\Entity\Client;
use App\Entity\Voiture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new Administrateur();
        $admin->setEmail('admin@karhabti.com');
        $admin->setNom('Admin Karhabti');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        for ($i = 1; $i <= 3; $i++) {
            $client = new Client();
            $client->setEmail("client{$i}@test.com");
            $client->setNom("Client {$i}");
            $client->setPassword($this->passwordHasher->hashPassword($client, 'password'));
            $client->setAdresse("{$i} Rue de Test, Tunis");
            $client->setNumTeleph("2" . rand(1000000, 9999999));
            $manager->persist($client);
        }

        $voitures = [
            ['BMW', 'Serie 3', 45000, 80, 2020],
            ['Mercedes', 'Classe C', 50000, 90, 2021],
            ['Audi', 'A4', 42000, 75, 2019],
            ['Renault', 'Clio', 15000, 40, 2022],
            ['Peugeot', '208', 18000, 45, 2021],
        ];

        foreach ($voitures as $index => $data) {
            $voiture = new Voiture();
            $voiture->setMatricule('TU-' . (1000 + $index) . '-' . rand(10, 99));
            $voiture->setMarque($data[0]);
            $voiture->setModele($data[1]);
            $voiture->setPrixAchat($data[2]);
            $voiture->setPrixLocationJour($data[3]);
            $voiture->setStatut('disponible');
            $voiture->setKilometrage(rand(5000, 50000));
            $voiture->setAnnee($data[4]);
            $voiture->setPhoto('https://via.placeholder.com/400x300?text=' . urlencode($data[0] . ' ' . $data[1]));
            $voiture->setDocuments('{"carte_grise":"complet","assurance":"valide","vignette":"2025"}');
            $manager->persist($voiture);
        }

        $manager->flush();
    }
}
