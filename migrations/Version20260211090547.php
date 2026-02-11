<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260211090547 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE PartnerAction (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, actionType VARCHAR(100) NOT NULL, parameters CLOB DEFAULT NULL, result CLOB DEFAULT NULL, status VARCHAR(20) NOT NULL, executedAt DATETIME DEFAULT NULL, createdAt DATETIME NOT NULL, partnerConnector_id INTEGER NOT NULL, CONSTRAINT FK_6A1A82A842F7880D FOREIGN KEY (partnerConnector_id) REFERENCES PartnerConnector (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6A1A82A842F7880D ON PartnerAction (partnerConnector_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE PartnerAction');
    }
}
