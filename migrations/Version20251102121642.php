<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251102121642 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD genre VARCHAR(20) DEFAULT NULL, ADD situation_familiale VARCHAR(30) DEFAULT NULL, ADD date_naissance DATE DEFAULT NULL, ADD pays_residence VARCHAR(100) DEFAULT NULL, ADD nationalite VARCHAR(100) DEFAULT NULL, ADD ville_actuelle VARCHAR(100) DEFAULT NULL, ADD telephone VARCHAR(20) DEFAULT NULL, ADD niveau_etudes VARCHAR(50) DEFAULT NULL, ADD diplomes_obtenus LONGTEXT DEFAULT NULL, ADD domaine_expertise VARCHAR(100) DEFAULT NULL, ADD competences_techniques LONGTEXT DEFAULT NULL, ADD certifications LONGTEXT DEFAULT NULL, ADD langues_parlees LONGTEXT DEFAULT NULL, ADD statut_emploi VARCHAR(50) DEFAULT NULL, ADD poste_actuel VARCHAR(100) DEFAULT NULL, ADD annees_experience INT DEFAULT NULL, ADD secteur_activite VARCHAR(100) DEFAULT NULL, ADD revenu_mensuel_souhaite INT DEFAULT NULL, ADD entreprise_actuelle VARCHAR(100) DEFAULT NULL, ADD objectifs_professionnels LONGTEXT DEFAULT NULL, ADD ce_que_vous_savez_faire LONGTEXT DEFAULT NULL, ADD ce_que_vous_aimez_faire LONGTEXT DEFAULT NULL, ADD talent_cache VARCHAR(255) DEFAULT NULL, ADD pret_ase_former TINYINT(1) DEFAULT NULL, ADD domaines_formation_souhaites LONGTEXT DEFAULT NULL, ADD pret_avous_exporter TINYINT(1) DEFAULT NULL, ADD pays_exportation_preference LONGTEXT DEFAULT NULL, ADD disponible_pour_missions TINYINT(1) DEFAULT NULL, ADD recherche_emploi TINYINT(1) DEFAULT NULL, ADD recherche_investisseurs TINYINT(1) DEFAULT NULL, ADD recherche_partenaires TINYINT(1) DEFAULT NULL, ADD sans_emploi TINYINT(1) DEFAULT NULL, ADD linkedin_url VARCHAR(255) DEFAULT NULL, ADD site_web_url VARCHAR(255) DEFAULT NULL, ADD portfolio_url VARCHAR(255) DEFAULT NULL, ADD centres_interet LONGTEXT DEFAULT NULL, ADD disponibilite VARCHAR(50) DEFAULT NULL, ADD mobilite_geographique VARCHAR(50) DEFAULT NULL, ADD bio LONGTEXT DEFAULT NULL, ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` DROP genre, DROP situation_familiale, DROP date_naissance, DROP pays_residence, DROP nationalite, DROP ville_actuelle, DROP telephone, DROP niveau_etudes, DROP diplomes_obtenus, DROP domaine_expertise, DROP competences_techniques, DROP certifications, DROP langues_parlees, DROP statut_emploi, DROP poste_actuel, DROP annees_experience, DROP secteur_activite, DROP revenu_mensuel_souhaite, DROP entreprise_actuelle, DROP objectifs_professionnels, DROP ce_que_vous_savez_faire, DROP ce_que_vous_aimez_faire, DROP talent_cache, DROP pret_ase_former, DROP domaines_formation_souhaites, DROP pret_avous_exporter, DROP pays_exportation_preference, DROP disponible_pour_missions, DROP recherche_emploi, DROP recherche_investisseurs, DROP recherche_partenaires, DROP sans_emploi, DROP linkedin_url, DROP site_web_url, DROP portfolio_url, DROP centres_interet, DROP disponibilite, DROP mobilite_geographique, DROP bio, DROP created_at, DROP updated_at');
    }
}
