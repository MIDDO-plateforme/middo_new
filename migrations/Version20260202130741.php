<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260202130741 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Workspace (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, slug VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, owner_id INTEGER NOT NULL, CONSTRAINT FK_F6582BA7E3C61F9 FOREIGN KEY (owner_id) REFERENCES User (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F6582BA989D9B62 ON Workspace (slug)');
        $this->addSql('CREATE INDEX IDX_F6582BA7E3C61F9 ON Workspace (owner_id)');
        $this->addSql('CREATE TABLE workspace_user (workspace_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY (workspace_id, user_id), CONSTRAINT FK_C971A58B82D40A1F FOREIGN KEY (workspace_id) REFERENCES Workspace (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C971A58BA76ED395 FOREIGN KEY (user_id) REFERENCES User (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_C971A58B82D40A1F ON workspace_user (workspace_id)');
        $this->addSql('CREATE INDEX IDX_C971A58BA76ED395 ON workspace_user (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE Workspace');
        $this->addSql('DROP TABLE workspace_user');
    }
}
