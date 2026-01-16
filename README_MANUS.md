# Projet Symfony - Karhabti (Achat et Location de Voitures)

Ce projet a été réparé et complété par Manus.

## Modifications apportées :
1.  **Compatibilité PHP** : Le projet a été rétrogradé de Symfony 7.3 à Symfony 6.4 pour assurer la compatibilité avec PHP 8.1.
2.  **Base de données** : Configuration passée de MySQL à SQLite pour une exécution immédiate sans configuration de serveur externe.
3.  **Backend Admin** : 
    *   Dashboard complet avec statistiques.
    *   Interfaces de gestion (Voitures, Commandes, Clients) stylisées avec Bootstrap.
4.  **Espace Client** :
    *   Dashboard client affichant les informations personnelles et l'historique des commandes.
    *   Système de commande (Achat/Location) fonctionnel depuis le catalogue.
5.  **Frontend** :
    *   Page d'accueil professionnelle.
    *   Catalogue complet avec filtres de disponibilité.
    *   Page de détails pour chaque voiture.

## Instructions d'installation :
1.  Assurez-vous d'avoir PHP 8.1+ et Composer installés.
2.  Installez les dépendances : `composer install`
3.  Initialisez la base de données :
    ```bash
    php bin/console doctrine:database:create
    php bin/console doctrine:schema:update --force
    php bin/console doctrine:fixtures:load --no-interaction
    ```
4.  Lancez le serveur : `symfony serve` ou `php -S localhost:8000 -t public`

## Identifiants de test :
*   **Admin** : `admin@karhabti.com` / `admin123`
*   **Client** : `client1@test.com` / `password`
