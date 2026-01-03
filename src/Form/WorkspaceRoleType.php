<?php

namespace App\Form;

use App\Entity\WorkspaceRole;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class WorkspaceRoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'label' => 'Utilisateur à inviter',
                'class' => User::class,
                'choice_label' => function(User $user) {
                    return $user->getEmail() . ' (' . $user->getUsername() . ')';
                },
                'attr' => [
                    'class' => 'form-select'
                ],
                'placeholder' => '-- Sélectionner un utilisateur --',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez sélectionner un utilisateur'
                    ])
                ]
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Rôle',
                'choices' => [
                    '👑 OWNER - Tous les droits (propriétaire)' => 'OWNER',
                    '⚡ ADMIN - Gestion complète sauf suppression' => 'ADMIN',
                    '👤 MEMBER - Collaboration standard' => 'MEMBER',
                    '👁️ VIEWER - Lecture seule' => 'VIEWER'
                ],
                'data' => 'MEMBER',
                'expanded' => true,
                'attr' => [
                    'class' => 'role-radio-group'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez sélectionner un rôle'
                    ])
                ],
                'help' => 'Le rôle détermine les permissions par défaut'
            ])
            ->add('customPermissions', ChoiceType::class, [
                'label' => 'Permissions personnalisées (optionnel)',
                'mapped' => false,
                'required' => false,
                'choices' => [
                    '📁 Workspace' => [
                        'Modifier le workspace' => 'workspace.edit',
                        'Supprimer le workspace' => 'workspace.delete',
                        'Inviter des collaborateurs' => 'workspace.invite',
                        'Gérer les rôles' => 'workspace.manage_roles'
                    ],
                    '📄 Documents' => [
                        'Créer des documents' => 'document.create',
                        'Modifier des documents' => 'document.edit',
                        'Supprimer des documents' => 'document.delete',
                        'Voir les documents' => 'document.view'
                    ],
                    '📊 Projets' => [
                        'Créer des projets' => 'project.create',
                        'Modifier des projets' => 'project.edit',
                        'Supprimer des projets' => 'project.delete',
                        'Voir les projets' => 'project.view',
                        'Gérer l\'équipe' => 'project.manage_team'
                    ],
                    '✅ Tâches' => [
                        'Créer des tâches' => 'task.create',
                        'Modifier des tâches' => 'task.edit',
                        'Supprimer des tâches' => 'task.delete',
                        'Voir les tâches' => 'task.view',
                        'Assigner des tâches' => 'task.assign'
                    ],
                    '💬 Commentaires' => [
                        'Créer des commentaires' => 'comment.create',
                        'Modifier des commentaires' => 'comment.edit',
                        'Supprimer des commentaires' => 'comment.delete',
                        'Voir les commentaires' => 'comment.view'
                    ],
                    '📈 Autres' => [
                        'Voir l\'historique' => 'activity.view',
                        'Voir les statistiques' => 'analytics.view',
                        'Gérer les paramètres' => 'settings.manage'
                    ]
                ],
                'multiple' => true,
                'expanded' => true,
                'attr' => [
                    'class' => 'custom-permissions-checkboxes'
                ],
                'help' => 'Laissez vide pour utiliser les permissions du rôle sélectionné'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkspaceRole::class,
            'attr' => [
                'novalidate' => 'novalidate',
                'class' => 'workspace-role-form needs-validation'
            ]
        ]);
    }
}
