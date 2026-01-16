<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260115171721 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA82EA2E54');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA537A1329');
        $this->addSql('DROP INDEX IDX_BF5476CA537A1329 ON notification');
        $this->addSql('DROP INDEX IDX_BF5476CA82EA2E54 ON notification');
        $this->addSql('ALTER TABLE notification DROP commande_id, DROP message_id, DROP title, CHANGE is_read is_read TINYINT(1) DEFAULT 0 NOT NULL, CHANGE created_at date_creation DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification ADD commande_id INT DEFAULT NULL, ADD message_id INT DEFAULT NULL, ADD title VARCHAR(255) NOT NULL, CHANGE is_read is_read TINYINT(1) NOT NULL, CHANGE date_creation created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        $this->addSql('CREATE INDEX IDX_BF5476CA537A1329 ON notification (message_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CA82EA2E54 ON notification (commande_id)');
    }
}
