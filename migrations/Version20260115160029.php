<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260115160029 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF019EB6921');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF019EB6921 FOREIGN KEY (client_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA19EB6921');
        $this->addSql('ALTER TABLE notification CHANGE client_id client_id INT NOT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA19EB6921 FOREIGN KEY (client_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF019EB6921');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF019EB6921 FOREIGN KEY (client_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA19EB6921');
        $this->addSql('ALTER TABLE notification CHANGE client_id client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA19EB6921 FOREIGN KEY (client_id) REFERENCES utilisateur (id)');
    }
}
