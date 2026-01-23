<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260123125138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create missions, notifications, project, project_members, users tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE missions (
            id SERIAL NOT NULL PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            budget NUMERIC(10, 2) NOT NULL,
            duration VARCHAR(100) NOT NULL,
            company VARCHAR(255) NOT NULL,
            skills TEXT NOT NULL,
            location VARCHAR(255) NOT NULL,
            urgency VARCHAR(50) NOT NULL,
            status VARCHAR(50) NOT NULL,
            created_at TIMESTAMP NOT NULL
        )');

        $this->addSql('CREATE TABLE users (
            id SERIAL NOT NULL PRIMARY KEY,
            email VARCHAR(180) NOT NULL,
            roles TEXT NOT NULL,
            password VARCHAR(255) NOT NULL
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_USERS_EMAIL ON users (email)');

        $this->addSql('CREATE TABLE notifications (
            id SERIAL NOT NULL PRIMARY KEY,
            type VARCHAR(50) NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            is_read BOOLEAN NOT NULL,
            created_at TIMESTAMP NOT NULL,
            user_id INTEGER NOT NULL,
            CONSTRAINT FK_NOTIF_USER FOREIGN KEY (user_id) REFERENCES users (id)
        )');
        $this->addSql('CREATE INDEX IDX_NOTIF_USER ON notifications (user_id)');

        $this->addSql('CREATE TABLE project (
            id SERIAL NOT NULL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT DEFAULT NULL,
            status VARCHAR(50) NOT NULL,
            budget NUMERIC(10, 2) DEFAULT NULL,
            start_date DATE DEFAULT NULL,
            end_date DATE DEFAULT NULL,
            created_at TIMESTAMP NOT NULL,
            updated_at TIMESTAMP NOT NULL,
            creator_id INTEGER NOT NULL,
            CONSTRAINT FK_PROJECT_CREATOR FOREIGN KEY (creator_id) REFERENCES users (id)
        )');
        $this->addSql('CREATE INDEX IDX_PROJECT_CREATOR ON project (creator_id)');

        $this->addSql('CREATE TABLE project_members (
            project_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            PRIMARY KEY (project_id, user_id),
            CONSTRAINT FK_PM_PROJECT FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE,
            CONSTRAINT FK_PM_USER FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
        )');
        $this->addSql('CREATE INDEX IDX_PM_PROJECT ON project_members (project_id)');
        $this->addSql('CREATE INDEX IDX_PM_USER ON project_members (user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE project_members');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE notifications');
        $this->addSql('DROP TABLE missions');
        $this->addSql('DROP TABLE users');
    }
}
