<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260102045401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE missions');
        $this->addSql('DROP TABLE notifications');
        $this->addSql('DROP TABLE users');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE missions (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL COLLATE "BINARY", description CLOB NOT NULL COLLATE "BINARY", budget NUMERIC(10, 2) NOT NULL, duration VARCHAR(100) NOT NULL COLLATE "BINARY", company VARCHAR(255) NOT NULL COLLATE "BINARY", skills CLOB NOT NULL COLLATE "BINARY" --(DC2Type:json)
        , location VARCHAR(255) NOT NULL COLLATE "BINARY", urgency VARCHAR(50) NOT NULL COLLATE "BINARY", status VARCHAR(50) NOT NULL COLLATE "BINARY", created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE TABLE notifications (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, type VARCHAR(50) NOT NULL COLLATE "BINARY", title VARCHAR(255) NOT NULL COLLATE "BINARY", message CLOB NOT NULL COLLATE "BINARY", is_read BOOLEAN NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_6000B0D3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6000B0D3A76ED395 ON notifications (user_id)');
        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL COLLATE "BINARY", name VARCHAR(100) NOT NULL COLLATE "BINARY", skills CLOB DEFAULT NULL COLLATE "BINARY" --(DC2Type:json)
        , rating NUMERIC(3, 2) DEFAULT NULL, roles CLOB NOT NULL COLLATE "BINARY" --(DC2Type:json)
        , password VARCHAR(255) NOT NULL COLLATE "BINARY", wallet NUMERIC(10, 2) DEFAULT NULL, projects INTEGER DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
    }
}
