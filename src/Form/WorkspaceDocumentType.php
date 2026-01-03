<?php

namespace App\Form;

use App\Entity\WorkspaceDocument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class WorkspaceDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du document',
                'attr' => [
                    'placeholder' => 'Ex: Rapport mensuel Janvier 2025',
                    'class' => 'form-control form-control-lg',
                    'maxlength' => 255
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le titre est obligatoire'
                    ]),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'Le titre doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le titre ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de document',
                'choices' => [
                    '📄 Texte / Note' => 'text',
                    '📎 Fichier' => 'file',
                    '🔗 Lien externe' => 'link',
                    '📋 Rapport' => 'report',
                    '📊 Présentation' => 'presentation',
                    '📑 Documentation' => 'documentation',
                    '💼 Contrat' => 'contract',
                    '📸 Média' => 'media'
                ],
                'attr' => [
                    'class' => 'form-select'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez sélectionner un type de document'
                    ])
                ]
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Catégorie',
                'required' => false,
                'choices' => [
                    'Administration' => 'administration',
                    'Financier' => 'finance',
                    'Marketing' => 'marketing',
                    'Technique' => 'technical',
                    'RH' => 'hr',
                    'Juridique' => 'legal',
                    'Communication' => 'communication',
                    'Design' => 'design',
                    'Autre' => 'other'
                ],
                'placeholder' => '-- Choisir une catégorie --',
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rédigez le contenu de votre document...',
                    'class' => 'form-control document-editor',
                    'rows' => 12
                ],
                'help' => 'Pour les fichiers, ce champ est optionnel'
            ])
            ->add('file', FileType::class, [
                'label' => 'Fichier à uploader',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'accept' => '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.gif'
                ],
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '10M',
                        'maxSizeMessage' => 'Le fichier ne peut pas dépasser {{ limit }}',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-powerpoint',
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                            'text/plain',
                            'image/jpeg',
                            'image/png',
                            'image/gif'
                        ],
                        'mimeTypesMessage' => 'Format de fichier non supporté'
                    ])
                ],
                'help' => 'Formats acceptés: PDF, Word, Excel, PowerPoint, Images (Max 10MB)'
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    '📝 Brouillon' => 'draft',
                    '🔍 En révision' => 'review',
                    '✅ Publié' => 'published',
                    '📦 Archivé' => 'archived'
                ],
                'data' => 'draft',
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkspaceDocument::class,
            'attr' => [
                'novalidate' => 'novalidate',
                'class' => 'workspace-document-form needs-validation',
                'enctype' => 'multipart/form-data'
            ]
        ]);
    }
}
