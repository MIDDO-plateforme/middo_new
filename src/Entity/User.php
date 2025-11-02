<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'Un compte existe déjà avec cet email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    private ?string $firstName = null;

    #[ORM\Column(length: 100)]
    private ?string $lastName = null;

    #[ORM\Column(length: 50)]
    private ?string $userType = null;

    // ========== INFORMATIONS PERSONNELLES ==========
    
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $genre = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $situationFamiliale = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $paysResidence = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $nationalite = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $villeActuelle = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telephone = null;

    // ========== FORMATION & COMPÉTENCES ==========
    
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $niveauEtudes = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $diplomesObtenus = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $domaineExpertise = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $competencesTechniques = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $certifications = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $languesParlees = null;

    // ========== SITUATION PROFESSIONNELLE ==========
    
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $statutEmploi = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $posteActuel = null;

    #[ORM\Column(nullable: true)]
    private ?int $anneesExperience = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $secteurActivite = null;

    #[ORM\Column(nullable: true)]
    private ?int $revenuMensuelSouhaite = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $entrepriseActuelle = null;

    // ========== ASPIRATIONS & TALENTS ==========
    
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $objectifsProfessionnels = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $ceQueVousSavezFaire = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $ceQueVousAimezFaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $talentCache = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $pretASeFormer = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $domainesFormationSouhaites = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $pretAVousExporter = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $paysExportationPreference = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $disponiblePourMissions = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $rechercheEmploi = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $rechercheInvestisseurs = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $recherchePartenaires = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $sansEmploi = null;

    // ========== RÉSEAU & PRÉFÉRENCES ==========
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $linkedinUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $siteWebUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $portfolioUrl = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $centresInteret = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $disponibilite = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $mobiliteGeographique = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->roles = ['ROLE_USER'];
    }

    // ========== GETTERS & SETTERS ==========

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getUserType(): ?string
    {
        return $this->userType;
    }

    public function setUserType(string $userType): static
    {
        $this->userType = $userType;
        return $this;
    }

    // Informations personnelles
    
    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(?string $genre): static
    {
        $this->genre = $genre;
        return $this;
    }

    public function getSituationFamiliale(): ?string
    {
        return $this->situationFamiliale;
    }

    public function setSituationFamiliale(?string $situationFamiliale): static
    {
        $this->situationFamiliale = $situationFamiliale;
        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTimeInterface $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;
        return $this;
    }

    public function getPaysResidence(): ?string
    {
        return $this->paysResidence;
    }

    public function setPaysResidence(?string $paysResidence): static
    {
        $this->paysResidence = $paysResidence;
        return $this;
    }

    public function getNationalite(): ?string
    {
        return $this->nationalite;
    }

    public function setNationalite(?string $nationalite): static
    {
        $this->nationalite = $nationalite;
        return $this;
    }

    public function getVilleActuelle(): ?string
    {
        return $this->villeActuelle;
    }

    public function setVilleActuelle(?string $villeActuelle): static
    {
        $this->villeActuelle = $villeActuelle;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }

    // Formation & Compétences

    public function getNiveauEtudes(): ?string
    {
        return $this->niveauEtudes;
    }

    public function setNiveauEtudes(?string $niveauEtudes): static
    {
        $this->niveauEtudes = $niveauEtudes;
        return $this;
    }

    public function getDiplomesObtenus(): ?string
    {
        return $this->diplomesObtenus;
    }

    public function setDiplomesObtenus(?string $diplomesObtenus): static
    {
        $this->diplomesObtenus = $diplomesObtenus;
        return $this;
    }

    public function getDomaineExpertise(): ?string
    {
        return $this->domaineExpertise;
    }

    public function setDomaineExpertise(?string $domaineExpertise): static
    {
        $this->domaineExpertise = $domaineExpertise;
        return $this;
    }

    public function getCompetencesTechniques(): ?string
    {
        return $this->competencesTechniques;
    }

    public function setCompetencesTechniques(?string $competencesTechniques): static
    {
        $this->competencesTechniques = $competencesTechniques;
        return $this;
    }

    public function getCertifications(): ?string
    {
        return $this->certifications;
    }

    public function setCertifications(?string $certifications): static
    {
        $this->certifications = $certifications;
        return $this;
    }

    public function getLanguesParlees(): ?string
    {
        return $this->languesParlees;
    }

    public function setLanguesParlees(?string $languesParlees): static
    {
        $this->languesParlees = $languesParlees;
        return $this;
    }

    // Situation professionnelle

    public function getStatutEmploi(): ?string
    {
        return $this->statutEmploi;
    }

    public function setStatutEmploi(?string $statutEmploi): static
    {
        $this->statutEmploi = $statutEmploi;
        return $this;
    }

    public function getPosteActuel(): ?string
    {
        return $this->posteActuel;
    }

    public function setPosteActuel(?string $posteActuel): static
    {
        $this->posteActuel = $posteActuel;
        return $this;
    }

    public function getAnneesExperience(): ?int
    {
        return $this->anneesExperience;
    }

    public function setAnneesExperience(?int $anneesExperience): static
    {
        $this->anneesExperience = $anneesExperience;
        return $this;
    }

    public function getSecteurActivite(): ?string
    {
        return $this->secteurActivite;
    }

    public function setSecteurActivite(?string $secteurActivite): static
    {
        $this->secteurActivite = $secteurActivite;
        return $this;
    }

    public function getRevenuMensuelSouhaite(): ?int
    {
        return $this->revenuMensuelSouhaite;
    }

    public function setRevenuMensuelSouhaite(?int $revenuMensuelSouhaite): static
    {
        $this->revenuMensuelSouhaite = $revenuMensuelSouhaite;
        return $this;
    }

    public function getEntrepriseActuelle(): ?string
    {
        return $this->entrepriseActuelle;
    }

    public function setEntrepriseActuelle(?string $entrepriseActuelle): static
    {
        $this->entrepriseActuelle = $entrepriseActuelle;
        return $this;
    }

    // Aspirations & Talents

    public function getObjectifsProfessionnels(): ?string
    {
        return $this->objectifsProfessionnels;
    }

    public function setObjectifsProfessionnels(?string $objectifsProfessionnels): static
    {
        $this->objectifsProfessionnels = $objectifsProfessionnels;
        return $this;
    }

    public function getCeQueVousSavezFaire(): ?string
    {
        return $this->ceQueVousSavezFaire;
    }

    public function setCeQueVousSavezFaire(?string $ceQueVousSavezFaire): static
    {
        $this->ceQueVousSavezFaire = $ceQueVousSavezFaire;
        return $this;
    }

    public function getCeQueVousAimezFaire(): ?string
    {
        return $this->ceQueVousAimezFaire;
    }

    public function setCeQueVousAimezFaire(?string $ceQueVousAimezFaire): static
    {
        $this->ceQueVousAimezFaire = $ceQueVousAimezFaire;
        return $this;
    }

    public function getTalentCache(): ?string
    {
        return $this->talentCache;
    }

    public function setTalentCache(?string $talentCache): static
    {
        $this->talentCache = $talentCache;
        return $this;
    }

    public function isPretASeFormer(): ?bool
    {
        return $this->pretASeFormer;
    }

    public function setPretASeFormer(?bool $pretASeFormer): static
    {
        $this->pretASeFormer = $pretASeFormer;
        return $this;
    }

    public function getDomainesFormationSouhaites(): ?string
    {
        return $this->domainesFormationSouhaites;
    }

    public function setDomainesFormationSouhaites(?string $domainesFormationSouhaites): static
    {
        $this->domainesFormationSouhaites = $domainesFormationSouhaites;
        return $this;
    }

    public function isPretAVousExporter(): ?bool
    {
        return $this->pretAVousExporter;
    }

    public function setPretAVousExporter(?bool $pretAVousExporter): static
    {
        $this->pretAVousExporter = $pretAVousExporter;
        return $this;
    }

    public function getPaysExportationPreference(): ?string
    {
        return $this->paysExportationPreference;
    }

    public function setPaysExportationPreference(?string $paysExportationPreference): static
    {
        $this->paysExportationPreference = $paysExportationPreference;
        return $this;
    }

    public function isDisponiblePourMissions(): ?bool
    {
        return $this->disponiblePourMissions;
    }

    public function setDisponiblePourMissions(?bool $disponiblePourMissions): static
    {
        $this->disponiblePourMissions = $disponiblePourMissions;
        return $this;
    }

    public function isRechercheEmploi(): ?bool
    {
        return $this->rechercheEmploi;
    }

    public function setRechercheEmploi(?bool $rechercheEmploi): static
    {
        $this->rechercheEmploi = $rechercheEmploi;
        return $this;
    }

    public function isRechercheInvestisseurs(): ?bool
    {
        return $this->rechercheInvestisseurs;
    }

    public function setRechercheInvestisseurs(?bool $rechercheInvestisseurs): static
    {
        $this->rechercheInvestisseurs = $rechercheInvestisseurs;
        return $this;
    }

    public function isRecherchePartenaires(): ?bool
    {
        return $this->recherchePartenaires;
    }

    public function setRecherchePartenaires(?bool $recherchePartenaires): static
    {
        $this->recherchePartenaires = $recherchePartenaires;
        return $this;
    }

    public function isSansEmploi(): ?bool
    {
        return $this->sansEmploi;
    }

    public function setSansEmploi(?bool $sansEmploi): static
    {
        $this->sansEmploi = $sansEmploi;
        return $this;
    }

    // Réseau & Préférences

    public function getLinkedinUrl(): ?string
    {
        return $this->linkedinUrl;
    }

    public function setLinkedinUrl(?string $linkedinUrl): static
    {
        $this->linkedinUrl = $linkedinUrl;
        return $this;
    }

    public function getSiteWebUrl(): ?string
    {
        return $this->siteWebUrl;
    }

    public function setSiteWebUrl(?string $siteWebUrl): static
    {
        $this->siteWebUrl = $siteWebUrl;
        return $this;
    }

    public function getPortfolioUrl(): ?string
    {
        return $this->portfolioUrl;
    }

    public function setPortfolioUrl(?string $portfolioUrl): static
    {
        $this->portfolioUrl = $portfolioUrl;
        return $this;
    }

    public function getCentresInteret(): ?string
    {
        return $this->centresInteret;
    }

    public function setCentresInteret(?string $centresInteret): static
    {
        $this->centresInteret = $centresInteret;
        return $this;
    }

    public function getDisponibilite(): ?string
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(?string $disponibilite): static
    {
        $this->disponibilite = $disponibilite;
        return $this;
    }

    public function getMobiliteGeographique(): ?string
    {
        return $this->mobiliteGeographique;
    }

    public function setMobiliteGeographique(?string $mobiliteGeographique): static
    {
        $this->mobiliteGeographique = $mobiliteGeographique;
        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
