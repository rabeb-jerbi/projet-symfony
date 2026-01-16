<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

// Load .env
(new Dotenv())->bootEnv(__DIR__ . '/../.env');

$databaseUrl = $_ENV['DATABASE_URL'];

// Create PDO connection
$pdo = new PDO($databaseUrl);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Hash password
$password = password_hash('admin123', PASSWORD_BCRYPT);

try {
    // Check if admin exists
    $stmt = $pdo->query("SELECT * FROM utilisateur WHERE email = 'admin@yourcar.tn'");
    $existingAdmin = $stmt->fetch();

    if ($existingAdmin) {
        // Update existing user to admin
        $sql = "UPDATE utilisateur SET roles = ?, type = ? WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['["ROLE_ADMIN"]', 'administrateur', 'admin@yourcar.tn']);
        echo "✓ User 'admin@yourcar.tn' has been updated to ADMIN\n";
    } else {
        // Create new admin
        $sql = "INSERT INTO utilisateur (email, roles, password, nom, type) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'admin@yourcar.tn',
            '["ROLE_ADMIN"]',
            $password,
            'Administrateur',
            'administrateur'
        ]);
        echo "✓ Admin user created successfully!\n";
    }

    echo "\nCredentials:\n";
    echo "Email: admin@yourcar.tn\n";
    echo "Password: admin123\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
