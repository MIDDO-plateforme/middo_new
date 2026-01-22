<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260122174543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE missions (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description CLOB NOT NULL, budget NUMERIC(10, 2) NOT NULL, duration VARCHAR(100) NOT NULL, company VARCHAR(255) NOT NULL, skills CLOB NOT NULL, location VARCHAR(255) NOT NULL, urgency VARCHAR(50) NOT NULL, status VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE notifications (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, message CLOB NOT NULL, is_read BOOLEAN NOT NULL, created_at DATETIME NOT NULL, user_id INTEGER NOT NULL, CONSTRAINT FK_6000B0D3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6000B0D3A76ED395 ON notifications (user_id)');
        $this->addSql('CREATE TABLE project (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, status VARCHAR(50) NOT NULL, budget NUMERIC(10, 2) DEFAULT NULL, start_date DATE DEFAULT NULL, end_date DATE DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, creator_id INTEGER NOT NULL, CONSTRAINT FK_2FB3D0EE61220EA6 FOREIGN KEY (creator_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE61220EA6 ON project (creator_id)');
        $this->addSql('CREATE TABLE project_members (project_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY (project_id, user_id), CONSTRAINT FK_D3BEDE9A166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D3BEDE9AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_D3BEDE9A166D1F9C ON project_members (project_id)');
        $this->addSql('CREATE INDEX IDX_D3BEDE9AA76ED395 ON project_members (user_id)');
        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, skills CLOB DEFAULT NULL, wallet DOUBLE PRECISION NOT NULL, rating DOUBLE PRECISION DEFAULT NULL, projects INTEGER NOT NULL, locale VARCHAR(10) NOT NULL, last_login_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE missions');
        $this->addSql('DROP TABLE notifications');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_members');
        $this->addSql('DROP TABLE users');
    }
}
