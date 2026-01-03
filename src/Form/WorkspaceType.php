<?php

namespace App\Form;

use App\Entity\Workspace;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class WorkspaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du workspace',
                'attr' => [
                    'placeholder' => 'Ex: Projet Marketing 2025',
                    'class' => 'form-control form-control-lg',
                    'maxlength' => 255
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nom du workspace est obligatoire'
                    ]),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Décrivez l\'objectif de ce workspace...',
                    'class' => 'form-control',
                    'rows' => 4
                ],
                'constraints' => [
                    new Assert\Length([
                        'max' => 2000,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('visibility', ChoiceType::class, [
                'label' => 'Visibilité',
                'choices' => [
                    '🔒 Privé - Uniquement les membres invités' => 'private',
                    '👥 Équipe - Visible par tous les collaborateurs' => 'team',
                    '🌐 Public - Accessible à tous' => 'public'
                ],
                'expanded' => true,
                'attr' => [
                    'class' => 'visibility-radio-group'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez choisir une visibilité'
                    ])
                ]
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Activer ce workspace',
                'required' => false,
                'data' => true,
                'attr' => [
                    'class' => 'form-check-input'
                ],
                'help' => 'Les workspaces inactifs ne sont visibles que par leur propriétaire'
            ])
            ->add('settings', ChoiceType::class, [
                'label' => 'Thème',
                'mapped' => false,
                'choices' => [
                    '☀️ Clair' => 'light',
                    '🌙 Sombre' => 'dark',
                    '🎨 Auto (système)' => 'auto'
                ],
                'data' => 'light',
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('aiPreferences', ChoiceType::class, [
                'label' => 'Fonctionnalités IA',
                'mapped' => false,
                'choices' => [
                    '💡 Suggestions automatiques' => 'auto_suggestions',
                    '😊 Analyse de sentiment' => 'sentiment_analysis',
                    '🤝 Matching intelligent' => 'smart_matching',
                    '📊 Métriques prédictives' => 'predictive_metrics'
                ],
                'multiple' => true,
                'expanded' => true,
                'data' => ['auto_suggestions', 'sentiment_analysis', 'smart_matching'],
                'attr' => [
                    'class' => 'ai-preferences-checkboxes'
                ],
                'help' => 'Activez les fonctionnalités IA pour améliorer la productivité'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Workspace::class,
            'attr' => [
                'novalidate' => 'novalidate',
                'class' => 'workspace-form needs-validation'
            ]
        ]);
    }
}
