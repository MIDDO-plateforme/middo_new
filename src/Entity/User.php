<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    #[ORM\Column(type: Types::STRING)]
    private ?string $password = null;

    #[ORM\Column(type: Types::STRING, length: 100, name: 'first_name')]
    private ?string $firstName = null;

    #[ORM\Column(type: Types::STRING, length: 100, name: 'last_name')]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::STRING, length: 50, name: 'user_type')]
    private ?string $userType = null;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true)]
    private ?string $genre = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true, name: 'situation_familiale')]
    private ?string $situationFamiliale = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true, name: 'date_naissance')]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, name: 'pays_residence')]
    private ?string $paysResidence = null;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true)]
    private ?string $nationalite = null;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, name: 'ville_actuelle')]
    private ?string $villeActuelle = null;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, name: 'niveau_etudes')]
    private ?string $niveauEtudes = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, name: 'domaine_expertise')]
    private ?string $domaineExpertise = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, name: 'langues_parlees')]
    private ?string $languesParlees = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $certifications = null;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, name: 'statut_emploi')]
    private ?string $statutEmploi = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, name: 'poste_actuel')]
    private ?string $posteActuel = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true, name: 'annees_experience')]
    private ?int $anneesExperience = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, name: 'secteur_activite')]
    private ?string $secteurActivite = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, name: 'objectifs_professionnels')]
    private ?string $objectifsProfessionnels = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, name: 'ce_que_vous_savez_faire')]
    private ?string $ceQueVousSavezFaire = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, name: 'ce_que_vous_aimez_faire')]
    private ?string $ceQueVousAimezFaire = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, name: 'talent_cache')]
    private ?string $talentCache = null;

    #[ORM\Column(type: Types::BOOLEAN, name: 'pret_a_se_former')]
    private ?bool $pretASeFormer = false;

    #[ORM\Column(type: Types::BOOLEAN, name: 'disponible_pour_missions')]
    private ?bool $disponiblePourMissions = false;

    #[ORM\Column(type: Types::BOOLEAN, name: 'recherche_emploi')]
    private ?bool $rechercheEmploi = false;

    #[ORM\Column(type: Types::BOOLEAN, name: 'recherche_investisseurs')]
    private ?bool $rechercheInvestisseurs = false;

    #[ORM\Column(type: Types::BOOLEAN, name: 'recherche_partenaires')]
    private ?bool $recherchePartenaires = false;

    #[ORM\Column(type: Types::BOOLEAN, name: 'sans_emploi')]
    private ?bool $sansEmploi = false;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, name: 'linkedin_url')]
    private ?string $linkedinUrl = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, name: 'site_web_url')]
    private ?string $siteWebUrl = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, name: 'portfolio_url')]
    private ?string $portfolioUrl = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bio = null;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Message::class)]
    private Collection $sentMessages;

    #[ORM\OneToMany(mappedBy: 'recipient', targetEntity: Message::class)]
    private Collection $receivedMessages;

    public function __construct()
    {
        $this->pretASeFormer = false;
        $this->disponiblePourMissions = false;
        $this->rechercheEmploi = false;
        $this->rechercheInvestisseurs = false;
        $this->recherchePartenaires = false;
        $this->sansEmploi = false;
        $this->sentMessages = new ArrayCollection();
        $this->receivedMessages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
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

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getUserType(): ?string
    {
        return $this->userType;
    }

    public function setUserType(string $userType): self
    {
        $this->userType = $userType;
        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(?string $genre): self
    {
        $this->genre = $genre;
        return $this;
    }

    public function getSituationFamiliale(): ?string
    {
        return $this->situationFamiliale;
    }

    public function setSituationFamiliale(?string $situationFamiliale): self
    {
        $this->situationFamiliale = $situationFamiliale;
        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;
        return $this;
    }

    public function getPaysResidence(): ?string
    {
        return $this->paysResidence;
    }

    public function setPaysResidence(?string $paysResidence): self
    {
        $this->paysResidence = $paysResidence;
        return $this;
    }

    public function getNationalite(): ?string
    {
        return $this->nationalite;
    }

    public function setNationalite(?string $nationalite): self
    {
        $this->nationalite = $nationalite;
        return $this;
    }

    public function getVilleActuelle(): ?string
    {
        return $this->villeActuelle;
    }

    public function setVilleActuelle(?string $villeActuelle): self
    {
        $this->villeActuelle = $villeActuelle;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getNiveauEtudes(): ?string
    {
        return $this->niveauEtudes;
    }

    public function setNiveauEtudes(?string $niveauEtudes): self
    {
        $this->niveauEtudes = $niveauEtudes;
        return $this;
    }

    public function getDomaineExpertise(): ?string
    {
        return $this->domaineExpertise;
    }

    public function setDomaineExpertise(?string $domaineExpertise): self
    {
        $this->domaineExpertise = $domaineExpertise;
        return $this;
    }

    public function getLanguesParlees(): ?string
    {
        return $this->languesParlees;
    }

    public function setLanguesParlees(?string $languesParlees): self
    {
        $this->languesParlees = $languesParlees;
        return $this;
    }

    public function getCertifications(): ?string
    {
        return $this->certifications;
    }

    public function setCertifications(?string $certifications): self
    {
        $this->certifications = $certifications;
        return $this;
    }

    public function getStatutEmploi(): ?string
    {
        return $this->statutEmploi;
    }

    public function setStatutEmploi(?string $statutEmploi): self
    {
        $this->statutEmploi = $statutEmploi;
        return $this;
    }

    public function getPosteActuel(): ?string
    {
        return $this->posteActuel;
    }

    public function setPosteActuel(?string $posteActuel): self
    {
        $this->posteActuel = $posteActuel;
        return $this;
    }

    public function getAnneesExperience(): ?int
    {
        return $this->anneesExperience;
    }

    public function setAnneesExperience(?int $anneesExperience): self
    {
        $this->anneesExperience = $anneesExperience;
        return $this;
    }

    public function getSecteurActivite(): ?string
    {
        return $this->secteurActivite;
    }

    public function setSecteurActivite(?string $secteurActivite): self
    {
        $this->secteurActivite = $secteurActivite;
        return $this;
    }

    public function getObjectifsProfessionnels(): ?string
    {
        return $this->objectifsProfessionnels;
    }

    public function setObjectifsProfessionnels(?string $objectifsProfessionnels): self
    {
        $this->objectifsProfessionnels = $objectifsProfessionnels;
        return $this;
    }

    public function getCeQueVousSavezFaire(): ?string
    {
        return $this->ceQueVousSavezFaire;
    }

    public function setCeQueVousSavezFaire(?string $ceQueVousSavezFaire): self
    {
        $this->ceQueVousSavezFaire = $ceQueVousSavezFaire;
        return $this;
    }

    public function getCeQueVousAimezFaire(): ?string
    {
        return $this->ceQueVousAimezFaire;
    }

    public function setCeQueVousAimezFaire(?string $ceQueVousAimezFaire): self
    {
        $this->ceQueVousAimezFaire = $ceQueVousAimezFaire;
        return $this;
    }

    public function getTalentCache(): ?string
    {
        return $this->talentCache;
    }

    public function setTalentCache(?string $talentCache): self
    {
        $this->talentCache = $talentCache;
        return $this;
    }

    public function isPretASeFormer(): ?bool
    {
        return $this->pretASeFormer;
    }

    public function setPretASeFormer(bool $pretASeFormer): self
    {
        $this->pretASeFormer = $pretASeFormer;
        return $this;
    }

    public function isDisponiblePourMissions(): ?bool
    {
        return $this->disponiblePourMissions;
    }

    public function setDisponiblePourMissions(bool $disponiblePourMissions): self
    {
        $this->disponiblePourMissions = $disponiblePourMissions;
        return $this;
    }

    public function isRechercheEmploi(): ?bool
    {
        return $this->rechercheEmploi;
    }

    public function setRechercheEmploi(bool $rechercheEmploi): self
    {
        $this->rechercheEmploi = $rechercheEmploi;
        return $this;
    }

    public function isRechercheInvestisseurs(): ?bool
    {
        return $this->rechercheInvestisseurs;
    }

    public function setRechercheInvestisseurs(bool $rechercheInvestisseurs): self
    {
        $this->rechercheInvestisseurs = $rechercheInvestisseurs;
        return $this;
    }

    public function isRecherchePartenaires(): ?bool
    {
        return $this->recherchePartenaires;
    }

    public function setRecherchePartenaires(bool $recherchePartenaires): self
    {
        $this->recherchePartenaires = $recherchePartenaires;
        return $this;
    }

    public function isSansEmploi(): ?bool
    {
        return $this->sansEmploi;
    }

    public function setSansEmploi(bool $sansEmploi): self
    {
        $this->sansEmploi = $sansEmploi;
        return $this;
    }

    public function getLinkedinUrl(): ?string
    {
        return $this->linkedinUrl;
    }

    public function setLinkedinUrl(?string $linkedinUrl): self
    {
        $this->linkedinUrl = $linkedinUrl;
        return $this;
    }

    public function getSiteWebUrl(): ?string
    {
        return $this->siteWebUrl;
    }

    public function setSiteWebUrl(?string $siteWebUrl): self
    {
        $this->siteWebUrl = $siteWebUrl;
        return $this;
    }

    public function getPortfolioUrl(): ?string
    {
        return $this->portfolioUrl;
    }

    public function setPortfolioUrl(?string $portfolioUrl): self
    {
        $this->portfolioUrl = $portfolioUrl;
        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;
        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getSentMessages(): Collection
    {
        return $this->sentMessages;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getReceivedMessages(): Collection
    {
        return $this->receivedMessages;
    }
}
