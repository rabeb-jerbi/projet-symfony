<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260115163256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, sender_id INT NOT NULL, recipient_id INT NOT NULL, root_id INT DEFAULT NULL, subject VARCHAR(150) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_read TINYINT(1) NOT NULL, INDEX IDX_B6BD307FF624B39D (sender_id), INDEX IDX_B6BD307FE92F8F78 (recipient_id), INDEX IDX_B6BD307F79066886 (root_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FE92F8F78 FOREIGN KEY (recipient_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F79066886 FOREIGN KEY (root_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF019EB6921');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF019EB6921 FOREIGN KEY (client_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA19EB6921');
        $this->addSql('DROP INDEX IDX_BF5476CA19EB6921 ON notification');
        $this->addSql('ALTER TABLE notification ADD commande_id INT DEFAULT NULL, ADD message_id INT DEFAULT NULL, ADD title VARCHAR(255) NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD is_read TINYINT(1) NOT NULL, DROP date_creation, CHANGE client_id recipient_id INT NOT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAE92F8F78 FOREIGN KEY (recipient_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        $this->addSql('CREATE INDEX IDX_BF5476CAE92F8F78 ON notification (recipient_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CA82EA2E54 ON notification (commande_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CA537A1329 ON notification (message_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA537A1329');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FE92F8F78');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F79066886');
        $this->addSql('DROP TABLE message');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF019EB6921');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF019EB6921 FOREIGN KEY (client_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAE92F8F78');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA82EA2E54');
        $this->addSql('DROP INDEX IDX_BF5476CAE92F8F78 ON notification');
        $this->addSql('DROP INDEX IDX_BF5476CA82EA2E54 ON notification');
        $this->addSql('DROP INDEX IDX_BF5476CA537A1329 ON notification');
        $this->addSql('ALTER TABLE notification ADD date_creation DATETIME NOT NULL, DROP commande_id, DROP message_id, DROP title, DROP created_at, DROP is_read, CHANGE recipient_id client_id INT NOT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA19EB6921 FOREIGN KEY (client_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_BF5476CA19EB6921 ON notification (client_id)');
    }
}
