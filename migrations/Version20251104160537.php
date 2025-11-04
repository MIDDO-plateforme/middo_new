<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251104160537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD pret_a_se_former TINYINT(1) NOT NULL, DROP diplomes_obtenus, DROP competences_techniques, DROP revenu_mensuel_souhaite, DROP entreprise_actuelle, DROP pret_ase_former, DROP domaines_formation_souhaites, DROP pret_avous_exporter, DROP pays_exportation_preference, DROP centres_interet, DROP disponibilite, DROP mobilite_geographique, DROP created_at, DROP updated_at, CHANGE situation_familiale situation_familiale VARCHAR(50) DEFAULT NULL, CHANGE niveau_etudes niveau_etudes VARCHAR(100) DEFAULT NULL, CHANGE domaine_expertise domaine_expertise VARCHAR(255) DEFAULT NULL, CHANGE langues_parlees langues_parlees VARCHAR(255) DEFAULT NULL, CHANGE statut_emploi statut_emploi VARCHAR(100) DEFAULT NULL, CHANGE poste_actuel poste_actuel VARCHAR(255) DEFAULT NULL, CHANGE secteur_activite secteur_activite VARCHAR(255) DEFAULT NULL, CHANGE disponible_pour_missions disponible_pour_missions TINYINT(1) NOT NULL, CHANGE recherche_emploi recherche_emploi TINYINT(1) NOT NULL, CHANGE recherche_investisseurs recherche_investisseurs TINYINT(1) NOT NULL, CHANGE recherche_partenaires recherche_partenaires TINYINT(1) NOT NULL, CHANGE sans_emploi sans_emploi TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD diplomes_obtenus LONGTEXT DEFAULT NULL, ADD competences_techniques LONGTEXT DEFAULT NULL, ADD revenu_mensuel_souhaite INT DEFAULT NULL, ADD entreprise_actuelle VARCHAR(100) DEFAULT NULL, ADD pret_ase_former TINYINT(1) DEFAULT NULL, ADD domaines_formation_souhaites LONGTEXT DEFAULT NULL, ADD pret_avous_exporter TINYINT(1) DEFAULT NULL, ADD pays_exportation_preference LONGTEXT DEFAULT NULL, ADD centres_interet LONGTEXT DEFAULT NULL, ADD disponibilite VARCHAR(50) DEFAULT NULL, ADD mobilite_geographique VARCHAR(50) DEFAULT NULL, ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD updated_at DATETIME DEFAULT NULL, DROP pret_a_se_former, CHANGE situation_familiale situation_familiale VARCHAR(30) DEFAULT NULL, CHANGE niveau_etudes niveau_etudes VARCHAR(50) DEFAULT NULL, CHANGE domaine_expertise domaine_expertise VARCHAR(100) DEFAULT NULL, CHANGE langues_parlees langues_parlees LONGTEXT DEFAULT NULL, CHANGE statut_emploi statut_emploi VARCHAR(50) DEFAULT NULL, CHANGE poste_actuel poste_actuel VARCHAR(100) DEFAULT NULL, CHANGE secteur_activite secteur_activite VARCHAR(100) DEFAULT NULL, CHANGE disponible_pour_missions disponible_pour_missions TINYINT(1) DEFAULT NULL, CHANGE recherche_emploi recherche_emploi TINYINT(1) DEFAULT NULL, CHANGE recherche_investisseurs recherche_investisseurs TINYINT(1) DEFAULT NULL, CHANGE recherche_partenaires recherche_partenaires TINYINT(1) DEFAULT NULL, CHANGE sans_emploi sans_emploi TINYINT(1) DEFAULT NULL');
    }
}
