<?php

namespace App\Form;

use App\Entity\WorkspaceProject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class WorkspaceProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du projet',
                'attr' => [
                    'placeholder' => 'Ex: Refonte site web Q1 2025',
                    'class' => 'form-control form-control-lg',
                    'maxlength' => 255
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nom du projet est obligatoire'
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
                'label' => 'Description du projet',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Décrivez les objectifs, le contexte et les livrables attendus...',
                    'class' => 'form-control',
                    'rows' => 5
                ],
                'constraints' => [
                    new Assert\Length([
                        'max' => 5000,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    '📋 Planification' => 'planning',
                    '🚀 En cours' => 'in_progress',
                    '⏸️ En pause' => 'on_hold',
                    '✅ Terminé' => 'completed',
                    '❌ Annulé' => 'cancelled'
                ],
                'data' => 'planning',
                'attr' => [
                    'class' => 'form-select'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez sélectionner un statut'
                    ])
                ]
            ])
            ->add('priority', ChoiceType::class, [
                'label' => 'Priorité',
                'choices' => [
                    '🔴 Critique' => 'critical',
                    '🟠 Haute' => 'high',
                    '🟡 Moyenne' => 'medium',
                    '🟢 Basse' => 'low'
                ],
                'data' => 'medium',
                'attr' => [
                    'class' => 'form-select'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez sélectionner une priorité'
                    ])
                ]
            ])
            ->add('progress', IntegerType::class, [
                'label' => 'Progression (%)',
                'data' => 0,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'max' => 100,
                    'step' => 5
                ],
                'help' => 'Sera mis à jour automatiquement en fonction des tâches',
                'constraints' => [
                    new Assert\Range([
                        'min' => 0,
                        'max' => 100,
                        'notInRangeMessage' => 'La progression doit être entre {{ min }} et {{ max }}%'
                    ])
                ]
            ])
            ->add('budget', NumberType::class, [
                'label' => 'Budget (€)',
                'required' => false,
                'attr' => [
                    'placeholder' => '0.00',
                    'class' => 'form-control',
                    'step' => '0.01',
                    'min' => '0'
                ],
                'help' => 'Budget total alloué au projet',
                'constraints' => [
                    new Assert\PositiveOrZero([
                        'message' => 'Le budget doit être positif ou zéro'
                    ])
                ]
            ])
            ->add('spent', NumberType::class, [
                'label' => 'Dépensé (€)',
                'required' => false,
                'data' => 0,
                'attr' => [
                    'placeholder' => '0.00',
                    'class' => 'form-control',
                    'step' => '0.01',
                    'min' => '0'
                ],
                'help' => 'Montant déjà dépensé',
                'constraints' => [
                    new Assert\PositiveOrZero([
                        'message' => 'Le montant dépensé doit être positif ou zéro'
                    ])
                ]
            ])
            ->add('startDate', DateTimeType::class, [
                'label' => 'Date de début',
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ],
                'input' => 'datetime_immutable'
            ])
            ->add('deadline', DateTimeType::class, [
                'label' => 'Date limite',
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ],
                'input' => 'datetime_immutable',
                'help' => 'Date de livraison prévue'
            ])
            ->add('dashboardWidgets', ChoiceType::class, [
                'label' => 'Widgets du dashboard',
                'mapped' => false,
                'choices' => [
                    '📊 Aperçu des tâches' => 'tasks_overview',
                    '👥 Membres de l\'équipe' => 'team_members',
                    '💰 Suivi du budget' => 'budget_tracker',
                    '📅 Timeline du projet' => 'timeline',
                    '📈 Activité récente' => 'recent_activity',
                    '⚠️ Alertes et risques' => 'alerts',
                    '📉 Métriques IA' => 'ai_metrics'
                ],
                'multiple' => true,
                'expanded' => true,
                'data' => ['tasks_overview', 'team_members', 'budget_tracker', 'timeline', 'recent_activity'],
                'attr' => [
                    'class' => 'dashboard-widgets-checkboxes'
                ],
                'help' => 'Personnalisez les widgets affichés sur le dashboard du projet'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkspaceProject::class,
            'attr' => [
                'novalidate' => 'novalidate',
                'class' => 'workspace-project-form needs-validation'
            ]
        ]);
    }
}
