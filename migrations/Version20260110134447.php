<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260110134447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_B1DC7A1E581C63DD');
        $this->addSql('DROP INDEX IDX_B1DC7A1E581C63DD ON paiement');
        $this->addSql('ALTER TABLE paiement ADD commande_id INT NOT NULL, DROP realtion_id');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1E82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('CREATE INDEX IDX_B1DC7A1E82EA2E54 ON paiement (commande_id)');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_EMAIL ON utilisateur');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_B1DC7A1E82EA2E54');
        $this->addSql('DROP INDEX IDX_B1DC7A1E82EA2E54 ON paiement');
        $this->addSql('ALTER TABLE paiement ADD realtion_id INT DEFAULT NULL, DROP commande_id');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1E581C63DD FOREIGN KEY (realtion_id) REFERENCES commande (id)');
        $this->addSql('CREATE INDEX IDX_B1DC7A1E581C63DD ON paiement (realtion_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON utilisateur (email)');
    }
}
