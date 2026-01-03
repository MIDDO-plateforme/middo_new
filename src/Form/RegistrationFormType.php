<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ========== SECTION 1 : INFORMATIONS DE BASE (OBLIGATOIRES) ==========
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'attr' => ['class' => 'form-control', 'placeholder' => 'votre@email.com'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer une adresse email']),
                ],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Votre prénom'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre prénom']),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Votre nom'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre nom']),
                ],
            ])
            ->add('userType', ChoiceType::class, [
                'label' => 'Type de profil',
                'choices' => [
                    'Entrepreneur' => 'Entrepreneur',
                    'Investisseur' => 'Investisseur',
                    'Entreprise' => 'Entreprise',
                    'Particulier' => 'Particulier',
                    'Association' => 'Association',
                    'Institution' => 'Institution',
                    'Inspecteur' => 'Inspecteur',
                ],
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Sélectionnez votre profil',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner un type de profil']),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'attr' => ['class' => 'form-control', 'autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez créer un mot de passe']),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'J\'accepte les conditions d\'utilisation',
                'mapped' => false,
                'constraints' => [
                    new IsTrue(['message' => 'Vous devez accepter les conditions d\'utilisation']),
                ],
                'attr' => ['class' => 'form-check-input'],
            ])

            // ========== SECTION 2 : INFORMATIONS PERSONNELLES (OPTIONNELLES) ==========
            ->add('genre', ChoiceType::class, [
                'label' => 'Genre',
                'required' => false,
                'choices' => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                    'Autre' => 'Autre',
                    'Ne souhaite pas préciser' => 'Non précisé',
                ],
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Sélectionnez...',
            ])
            ->add('situationFamiliale', ChoiceType::class, [
                'label' => 'Situation familiale',
                'required' => false,
                'choices' => [
                    'Célibataire' => 'Célibataire',
                    'Marié(e)' => 'Marié',
                    'Divorcé(e)' => 'Divorcé',
                    'Veuf/Veuve' => 'Veuf',
                    'Autre' => 'Autre',
                ],
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Sélectionnez...',
            ])
            ->add('dateNaissance', DateType::class, [
                'label' => 'Date de naissance',
                'required' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => '+33 6 12 34 56 78'],
            ])
            ->add('paysResidence', ChoiceType::class, [
                'label' => 'Pays de résidence',
                'required' => false,
                'choices' => $this->getCountries(),
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Sélectionnez votre pays...',
            ])
            ->add('nationalite', ChoiceType::class, [
                'label' => 'Nationalité',
                'required' => false,
                'choices' => $this->getCountries(),
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Sélectionnez votre nationalité...',
            ])
            ->add('villeActuelle', TextType::class, [
                'label' => 'Ville actuelle',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Paris, New York, Tokyo...'],
            ])

            // ========== SECTION 3 : FORMATION & COMPÉTENCES (OPTIONNELLES) ==========
            ->add('niveauEtudes', ChoiceType::class, [
                'label' => 'Niveau d\'études',
                'required' => false,
                'choices' => [
                    'Primaire' => 'Primaire',
                    'Secondaire' => 'Secondaire',
                    'Baccalauréat' => 'Baccalauréat',
                    'Licence / Bachelor' => 'Licence',
                    'Master' => 'Master',
                    'Doctorat / PhD' => 'Doctorat',
                    'Autre' => 'Autre',
                ],
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Sélectionnez...',
            ])
            ->add('domaineExpertise', TextType::class, [
                'label' => 'Domaine d\'expertise principal',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Marketing digital, Développement web, Finance...'],
            ])
            ->add('ceQueVousSavezFaire', TextareaType::class, [
                'label' => 'Ce que vous savez faire',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Décrivez vos compétences validées et votre expérience...',
                ],
            ])
            ->add('languesParlees', TextareaType::class, [
                'label' => 'Langues parlées',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Ex: Français (natif), Anglais (courant), Espagnol (intermédiaire)',
                ],
            ])
            ->add('certifications', TextareaType::class, [
                'label' => 'Certifications professionnelles',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Listez vos certifications, diplômes spécialisés...',
                ],
            ])

            // ========== SECTION 4 : SITUATION PROFESSIONNELLE (OPTIONNELLE) ==========
            ->add('statutEmploi', ChoiceType::class, [
                'label' => 'Statut professionnel',
                'required' => false,
                'choices' => [
                    'En emploi' => 'En emploi',
                    'Sans emploi' => 'Sans emploi',
                    'Étudiant' => 'Étudiant',
                    'Entrepreneur' => 'Entrepreneur',
                    'Freelance' => 'Freelance',
                    'Retraité' => 'Retraité',
                ],
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Sélectionnez...',
            ])
            ->add('posteActuel', TextType::class, [
                'label' => 'Poste actuel',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Chef de projet, Développeur, Consultant...'],
            ])
            ->add('anneesExperience', IntegerType::class, [
                'label' => 'Années d\'expérience',
                'required' => false,
                'attr' => ['class' => 'form-control', 'min' => 0, 'max' => 50],
            ])
            ->add('secteurActivite', TextType::class, [
                'label' => 'Secteur d\'activité',
                'required' => false,
                'attr' => ['class' => 'form-control', 'placeholder' => 'IT, Santé, Finance, E-commerce...'],
            ])

            // ========== SECTION 5 : ASPIRATIONS & RECHERCHES (OPTIONNELLES) ==========
            ->add('objectifsProfessionnels', TextareaType::class, [
                'label' => 'Vos objectifs professionnels',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Décrivez ce que vous souhaitez accomplir professionnellement...',
                ],
            ])
            ->add('ceQueVousAimezFaire', TextareaType::class, [
                'label' => 'Ce que vous aimez faire',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Vos passions professionnelles...',
                ],
            ])
            ->add('talentCache', TextType::class, [
                'label' => 'Votre talent caché',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Un talent que peu de gens connaissent...',
                ],
            ])
            ->add('pretASeFormer', CheckboxType::class, [
                'label' => 'Prêt(e) à vous former',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('domainesFormationSouhaites', TextareaType::class, [
                'label' => 'Domaines de formation souhaités',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 2,
                    'placeholder' => 'Ex: IA, Marketing digital, Gestion de projet...',
                ],
            ])
            ->add('pretAVousExporter', CheckboxType::class, [
                'label' => 'Prêt(e) à vous exporter',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('paysExportationPreference', TextareaType::class, [
                'label' => 'Pays de préférence pour expatriation',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 2,
                    'placeholder' => 'Ex: Canada, États-Unis, Allemagne...',
                ],
            ])
            ->add('disponiblePourMissions', CheckboxType::class, [
                'label' => 'Disponible pour missions / projets',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('rechercheEmploi', CheckboxType::class, [
                'label' => 'En recherche d\'emploi',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('sansEmploi', CheckboxType::class, [
                'label' => 'Actuellement sans emploi',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('rechercheInvestisseurs', CheckboxType::class, [
                'label' => 'En recherche d\'investisseurs',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('recherchePartenaires', CheckboxType::class, [
                'label' => 'En recherche de partenaires',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ])

            // ========== SECTION 6 : RÉSEAU & LIENS (OPTIONNELS) ==========
            ->add('linkedinUrl', UrlType::class, [
                'label' => 'Profil LinkedIn',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'https://www.linkedin.com/in/votre-profil',
                ],
            ])
            ->add('siteWebUrl', UrlType::class, [
                'label' => 'Site web personnel',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'https://votresite.com',
                ],
            ])
            ->add('portfolioUrl', UrlType::class, [
                'label' => 'Portfolio',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'https://portfolio.com',
                ],
            ])
            ->add('bio', TextareaType::class, [
                'label' => 'Bio / Présentation',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Présentez-vous en quelques lignes...',
                ],
            ])
            ->add('disponibilite', ChoiceType::class, [
                'label' => 'Disponibilité',
                'required' => false,
                'choices' => [
                    'Immédiate' => 'Immédiate',
                    'Dans 1 mois' => '1 mois',
                    'Dans 3 mois' => '3 mois',
                    'Dans 6 mois' => '6 mois',
                ],
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Sélectionnez...',
            ])
            ->add('mobiliteGeographique', ChoiceType::class, [
                'label' => 'Mobilité géographique',
                'required' => false,
                'choices' => [
                    'Locale uniquement' => 'Locale',
                    'Nationale' => 'Nationale',
                    'Internationale' => 'Internationale',
                    'Télétravail uniquement' => 'Télétravail',
                ],
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Sélectionnez...',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    private function getCountries(): array
    {
        return [
            'France' => 'France',
            'Belgique' => 'Belgique',
            'Suisse' => 'Suisse',
            'Canada' => 'Canada',
            'États-Unis' => 'États-Unis',
            'Royaume-Uni' => 'Royaume-Uni',
            'Allemagne' => 'Allemagne',
            'Espagne' => 'Espagne',
            'Italie' => 'Italie',
            'Portugal' => 'Portugal',
            'Maroc' => 'Maroc',
            'Algérie' => 'Algérie',
            'Tunisie' => 'Tunisie',
            'Sénégal' => 'Sénégal',
            'Côte d\'Ivoire' => 'Côte d\'Ivoire',
            'Cameroun' => 'Cameroun',
            'Mali' => 'Mali',
            'Burkina Faso' => 'Burkina Faso',
            'Niger' => 'Niger',
            'Bénin' => 'Bénin',
            'Togo' => 'Togo',
            'Gabon' => 'Gabon',
            'Congo' => 'Congo',
            'RDC' => 'RDC',
            'Madagascar' => 'Madagascar',
            'Maurice' => 'Maurice',
            'Autre' => 'Autre',
        ];
    }
}
