<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfileEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('profilePictureFile', FileType::class, [
                'label' => 'Photo de profil',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG, JPEG ou PNG)',
                        'maxSizeMessage' => 'L\'image ne doit pas dépasser 2 Mo',
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/jpeg,image/png,image/jpg'
                ],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('genre', ChoiceType::class, [
                'label' => 'Genre',
                'required' => false,
                'choices' => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                    'Autre' => 'Autre',
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
                'attr' => ['class' => 'form-control'],
            ])
            ->add('villeActuelle', TextType::class, [
                'label' => 'Ville actuelle',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('paysResidence', TextType::class, [
                'label' => 'Pays de résidence',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('nationalite', TextType::class, [
                'label' => 'Nationalité',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('niveauEtudes', ChoiceType::class, [
                'label' => 'Niveau d\'études',
                'required' => false,
                'choices' => [
                    'Primaire' => 'Primaire',
                    'Secondaire' => 'Secondaire',
                    'Licence' => 'Licence',
                    'Master' => 'Master',
                    'Doctorat' => 'Doctorat',
                ],
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Sélectionnez...',
            ])
            ->add('domaineExpertise', TextType::class, [
                'label' => 'Domaine d\'expertise',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('ceQueVousSavezFaire', TextareaType::class, [
                'label' => 'Ce que vous savez faire',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 4],
            ])
            ->add('languesParlees', TextareaType::class, [
                'label' => 'Langues parlées',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3],
            ])
            ->add('certifications', TextareaType::class, [
                'label' => 'Certifications',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3],
            ])
            ->add('statutEmploi', ChoiceType::class, [
                'label' => 'Statut professionnel',
                'required' => false,
                'choices' => [
                    'En emploi' => 'En emploi',
                    'Sans emploi' => 'Sans emploi',
                    'Étudiant' => 'Étudiant',
                    'Freelance' => 'Freelance',
                ],
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Sélectionnez...',
            ])
            ->add('posteActuel', TextType::class, [
                'label' => 'Poste actuel',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('anneesExperience', IntegerType::class, [
                'label' => 'Années d\'expérience',
                'required' => false,
                'attr' => ['class' => 'form-control', 'min' => 0],
            ])
            ->add('secteurActivite', TextType::class, [
                'label' => 'Secteur d\'activité',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('objectifsProfessionnels', TextareaType::class, [
                'label' => 'Objectifs professionnels',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 4],
            ])
            ->add('ceQueVousAimezFaire', TextareaType::class, [
                'label' => 'Ce que vous aimez faire',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3],
            ])
            ->add('talentCache', TextType::class, [
                'label' => 'Votre talent caché',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('pretASeFormer', CheckboxType::class, [
                'label' => 'Prêt(e) à vous former',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('pretAVousExporter', CheckboxType::class, [
                'label' => 'Prêt(e) à vous exporter',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('disponiblePourMissions', CheckboxType::class, [
                'label' => 'Disponible pour missions',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('rechercheEmploi', CheckboxType::class, [
                'label' => 'En recherche d\'emploi',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('sansEmploi', CheckboxType::class, [
                'label' => 'Sans emploi',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('linkedinUrl', UrlType::class, [
                'label' => 'LinkedIn',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('siteWebUrl', UrlType::class, [
                'label' => 'Site web',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('portfolioUrl', UrlType::class, [
                'label' => 'Portfolio',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('bio', TextareaType::class, [
                'label' => 'Bio / Présentation',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 4],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
