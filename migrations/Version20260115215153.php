<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260115215153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis ADD voiture_id INT NOT NULL');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0181A8BA FOREIGN KEY (voiture_id) REFERENCES voiture (id)');
        $this->addSql('CREATE INDEX IDX_8F91ABF0181A8BA ON avis (voiture_id)');
        $this->addSql('ALTER TABLE notification ADD commande_id INT DEFAULT NULL, ADD message_id INT DEFAULT NULL, ADD title VARCHAR(150) NOT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        $this->addSql('CREATE INDEX IDX_BF5476CA82EA2E54 ON notification (commande_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CA537A1329 ON notification (message_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0181A8BA');
        $this->addSql('DROP INDEX IDX_8F91ABF0181A8BA ON avis');
        $this->addSql('ALTER TABLE avis DROP voiture_id');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA82EA2E54');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA537A1329');
        $this->addSql('DROP INDEX IDX_BF5476CA82EA2E54 ON notification');
        $this->addSql('DROP INDEX IDX_BF5476CA537A1329 ON notification');
        $this->addSql('ALTER TABLE notification DROP commande_id, DROP message_id, DROP title');
    }
}
