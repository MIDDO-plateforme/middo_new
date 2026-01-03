<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkspaceActivityFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $workspace = $options['workspace'];

        $builder
            ->add('actionType', ChoiceType::class, [
                'label' => 'Type d\'action',
                'required' => false,
                'placeholder' => '-- Tous les types --',
                'choices' => [
                    '📁 Workspace' => [
                        'Workspace créé' => 'workspace_created',
                        'Workspace modifié' => 'workspace_updated'
                    ],
                    '📄 Documents' => [
                        'Document créé' => 'document_created',
                        'Document modifié' => 'document_updated',
                        'Document supprimé' => 'document_deleted'
                    ],
                    '📊 Projets' => [
                        'Projet créé' => 'project_created',
                        'Projet modifié' => 'project_updated',
                        'Projet supprimé' => 'project_deleted',
                        'Membre ajouté' => 'project_member_added',
                        'Membre retiré' => 'project_member_removed'
                    ],
                    '✅ Tâches' => [
                        'Tâche créée' => 'task_created',
                        'Tâche modifiée' => 'task_updated',
                        'Tâche supprimée' => 'task_deleted',
                        'Statut changé' => 'task_status_changed',
                        'Temps enregistré' => 'task_time_logged'
                    ],
                    '👥 Collaborateurs' => [
                        'Collaborateur invité' => 'collaborator_invited',
                        'Accès révoqué' => 'collaborator_revoked',
                        'Rôle modifié' => 'role_updated'
                    ],
                    '💬 Commentaires' => [
                        'Commentaire créé' => 'comment_created',
                        'Commentaire modifié' => 'comment_updated',
                        'Commentaire supprimé' => 'comment_deleted'
                    ]
                ],
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('entityType', ChoiceType::class, [
                'label' => 'Type d\'entité',
                'required' => false,
                'placeholder' => '-- Toutes les entités --',
                'choices' => [
                    '📄 Document' => 'document',
                    '📊 Projet' => 'project',
                    '✅ Tâche' => 'task',
                    '💬 Commentaire' => 'comment',
                    '🔐 Rôle' => 'role'
                ],
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('user', EntityType::class, [
                'label' => 'Utilisateur',
                'class' => User::class,
                'required' => false,
                'placeholder' => '-- Tous les utilisateurs --',
                'choice_label' => 'email',
                'attr' => [
                    'class' => 'form-select'
                ],
                'query_builder' => function($repository) use ($workspace) {
                    return $repository->createQueryBuilder('u')
                        ->leftJoin('u.workspaceCollaborations', 'w')
                        ->where('w.id = :workspace OR u.id = :owner')
                        ->setParameter('workspace', $workspace->getId())
                        ->setParameter('owner', $workspace->getUser()->getId())
                        ->orderBy('u.email', 'ASC');
                }
            ])
            ->add('dateFrom', DateType::class, [
                'label' => 'Du',
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('dateTo', DateType::class, [
                'label' => 'Au',
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
            'workspace' => null,
            'attr' => [
                'class' => 'activity-filter-form'
            ]
        ]);

        $resolver->setRequired('workspace');
    }
}
