<?php

namespace App\Form;

use App\Entity\WorkspaceComment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class WorkspaceCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Votre commentaire',
                'attr' => [
                    'placeholder' => 'Écrivez votre commentaire... (Utilisez @username pour mentionner quelqu\'un)',
                    'class' => 'form-control comment-textarea',
                    'rows' => 4
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le commentaire ne peut pas être vide'
                    ]),
                    new Assert\Length([
                        'min' => 1,
                        'max' => 5000,
                        'minMessage' => 'Le commentaire doit contenir au moins {{ limit }} caractère',
                        'maxMessage' => 'Le commentaire ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkspaceComment::class,
            'attr' => [
                'novalidate' => 'novalidate',
                'class' => 'workspace-comment-form'
            ]
        ]);
    }
}
