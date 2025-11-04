<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251104085344 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create message table for messaging system';
    }

    public function up(Schema $schema): void
    {
        // Création de la table message uniquement
        $this->addSql('CREATE TABLE message (
            id INT AUTO_INCREMENT NOT NULL, 
            sender_id INT NOT NULL, 
            recipient_id INT NOT NULL, 
            content LONGTEXT NOT NULL, 
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            is_read TINYINT(1) NOT NULL, 
            INDEX IDX_B6BD307FF624B39D (sender_id), 
            INDEX IDX_B6BD307FE92F8F78 (recipient_id), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FE92F8F78 FOREIGN KEY (recipient_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // Suppression de la table message
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FE92F8F78');
        $this->addSql('DROP TABLE message');
    }
}

