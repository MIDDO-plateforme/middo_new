<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260211090331 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE PartnerConnector (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, token CLOB DEFAULT NULL, settings CLOB DEFAULT NULL, status VARCHAR(20) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, user_id INTEGER NOT NULL, partnerApp_id INTEGER NOT NULL, CONSTRAINT FK_CC437F6AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_CC437F6AE72F64E2 FOREIGN KEY (partnerApp_id) REFERENCES PartnerApp (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_CC437F6AA76ED395 ON PartnerConnector (user_id)');
        $this->addSql('CREATE INDEX IDX_CC437F6AE72F64E2 ON PartnerConnector (partnerApp_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE PartnerConnector');
    }
}
