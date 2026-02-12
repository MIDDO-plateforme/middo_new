<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260212211108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Company (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, sector VARCHAR(100) DEFAULT NULL)');
        $this->addSql('CREATE TABLE IaPromptTemplate (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(180) NOT NULL, description CLOB DEFAULT NULL, prompt CLOB NOT NULL, category VARCHAR(80) DEFAULT NULL, active BOOLEAN DEFAULT 1 NOT NULL)');
        $this->addSql('CREATE TABLE PartnerAction (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, actionType VARCHAR(100) NOT NULL, parameters CLOB DEFAULT NULL, result CLOB DEFAULT NULL, status VARCHAR(20) NOT NULL, executedAt DATETIME DEFAULT NULL, createdAt DATETIME NOT NULL, partnerConnector_id INTEGER NOT NULL, CONSTRAINT FK_6A1A82A842F7880D FOREIGN KEY (partnerConnector_id) REFERENCES PartnerConnector (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6A1A82A842F7880D ON PartnerAction (partnerConnector_id)');
        $this->addSql('CREATE TABLE PartnerApp (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(150) NOT NULL, description CLOB DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, category VARCHAR(100) NOT NULL, apiBaseUrl VARCHAR(255) DEFAULT NULL, documentationUrl VARCHAR(255) DEFAULT NULL, isActive BOOLEAN NOT NULL)');
        $this->addSql('CREATE TABLE PartnerConnector (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, token CLOB DEFAULT NULL, settings CLOB DEFAULT NULL, status VARCHAR(20) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME DEFAULT NULL, user_id INTEGER NOT NULL, partnerApp_id INTEGER NOT NULL, CONSTRAINT FK_CC437F6AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_CC437F6AE72F64E2 FOREIGN KEY (partnerApp_id) REFERENCES PartnerApp (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_CC437F6AA76ED395 ON PartnerConnector (user_id)');
        $this->addSql('CREATE INDEX IDX_CC437F6AE72F64E2 ON PartnerConnector (partnerApp_id)');
        $this->addSql('CREATE TABLE Project (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, budget NUMERIC(10, 2) DEFAULT NULL, status VARCHAR(50) NOT NULL, createdAt DATETIME NOT NULL, owner_id INTEGER NOT NULL, CONSTRAINT FK_E00EE9727E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_E00EE9727E3C61F9 ON Project (owner_id)');
        $this->addSql('CREATE TABLE Skill (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) NOT NULL, category VARCHAR(50) DEFAULT NULL, level VARCHAR(20) DEFAULT NULL)');
        $this->addSql('CREATE TABLE "Transaction" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, amount NUMERIC(10, 2) NOT NULL, currency VARCHAR(10) NOT NULL, status VARCHAR(50) NOT NULL, transactionType VARCHAR(50) NOT NULL, createdAt DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE ai_interaction (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, interactionType VARCHAR(50) NOT NULL, prompt CLOB NOT NULL, response CLOB NOT NULL, model VARCHAR(100) NOT NULL, tokensUsed INTEGER DEFAULT NULL, metadata CLOB DEFAULT NULL, createdAt DATETIME NOT NULL, user_id INTEGER DEFAULT NULL, CONSTRAINT FK_5BEA4E18A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_5BEA4E18A76ED395 ON ai_interaction (user_id)');
        $this->addSql('CREATE TABLE message (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, content CLOB NOT NULL, created_at DATETIME NOT NULL, is_read BOOLEAN NOT NULL, sender_id INTEGER NOT NULL, recipient_id INTEGER NOT NULL, CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_B6BD307FE92F8F78 FOREIGN KEY (recipient_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_B6BD307FF624B39D ON message (sender_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FE92F8F78 ON message (recipient_id)');
        $this->addSql('CREATE TABLE notification (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, message CLOB NOT NULL, actionUrl VARCHAR(500) DEFAULT NULL, isRead BOOLEAN NOT NULL, createdAt DATETIME NOT NULL, readAt DATETIME DEFAULT NULL, metadata CLOB DEFAULT NULL, recipient_id INTEGER NOT NULL, sender_id INTEGER DEFAULT NULL, CONSTRAINT FK_BF5476CAE92F8F78 FOREIGN KEY (recipient_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BF5476CAF624B39D FOREIGN KEY (sender_id) REFERENCES user (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_BF5476CAE92F8F78 ON notification (recipient_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CAF624B39D ON notification (sender_id)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, user_type VARCHAR(50) NOT NULL, genre VARCHAR(20) DEFAULT NULL, situation_familiale VARCHAR(50) DEFAULT NULL, date_naissance DATE DEFAULT NULL, pays_residence VARCHAR(100) DEFAULT NULL, nationalite VARCHAR(100) DEFAULT NULL, ville_actuelle VARCHAR(100) DEFAULT NULL, telephone VARCHAR(20) DEFAULT NULL, niveau_etudes VARCHAR(100) DEFAULT NULL, domaine_expertise VARCHAR(255) DEFAULT NULL, langues_parlees VARCHAR(255) DEFAULT NULL, certifications CLOB DEFAULT NULL, statut_emploi VARCHAR(100) DEFAULT NULL, poste_actuel VARCHAR(255) DEFAULT NULL, annees_experience INTEGER DEFAULT NULL, secteur_activite VARCHAR(255) DEFAULT NULL, objectifs_professionnels CLOB DEFAULT NULL, ce_que_vous_savez_faire CLOB DEFAULT NULL, ce_que_vous_aimez_faire CLOB DEFAULT NULL, talent_cache VARCHAR(255) DEFAULT NULL, pret_a_se_former BOOLEAN NOT NULL, disponible_pour_missions BOOLEAN NOT NULL, recherche_emploi BOOLEAN NOT NULL, recherche_investisseurs BOOLEAN NOT NULL, recherche_partenaires BOOLEAN NOT NULL, sans_emploi BOOLEAN NOT NULL, linkedin_url VARCHAR(255) DEFAULT NULL, site_web_url VARCHAR(255) DEFAULT NULL, portfolio_url VARCHAR(255) DEFAULT NULL, bio CLOB DEFAULT NULL, domainesFormationSouhaites CLOB DEFAULT NULL, pretAVousExporter BOOLEAN NOT NULL, paysExportationPreference CLOB DEFAULT NULL, disponibilite VARCHAR(50) DEFAULT NULL, mobiliteGeographique VARCHAR(50) DEFAULT NULL, profilePicture VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE Company');
        $this->addSql('DROP TABLE IaPromptTemplate');
        $this->addSql('DROP TABLE PartnerAction');
        $this->addSql('DROP TABLE PartnerApp');
        $this->addSql('DROP TABLE PartnerConnector');
        $this->addSql('DROP TABLE Project');
        $this->addSql('DROP TABLE Skill');
        $this->addSql('DROP TABLE "Transaction"');
        $this->addSql('DROP TABLE ai_interaction');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE user');
    }
}
