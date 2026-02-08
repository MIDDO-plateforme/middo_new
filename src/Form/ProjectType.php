<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du projet',
                'attr' => [
                    'placeholder' => 'Ex: Plateforme e-commerce textile',
                    'maxlength' => 255,
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le titre est obligatoire']),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Le titre ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description détaillée',
                'attr' => [
                    'placeholder' => 'Décrivez votre projet, vos objectifs, votre vision...',
                    'rows' => 6,
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La description est obligatoire'])
                ]
            ])
            ->add('budget', NumberType::class, [
                'label' => 'Budget estimé (FCFA)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: 500000',
                    'class' => 'form-control',
                    'min' => 0,
                    'step' => 1000
                ],
                'html5' => true,
                'constraints' => [
                    new PositiveOrZero(['message' => 'Le budget doit être positif ou zéro'])
                ]
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut du projet',
                'choices' => [
                    'En préparation' => 'En préparation',
                    'En recherche de financement' => 'En recherche de financement',
                    'En cours' => 'En cours',
                    'Terminé' => 'Terminé',
                    'En pause' => 'En pause',
                    'Abandonné' => 'Abandonné'
                ],
                'attr' => [
                    'class' => 'form-select'
                ],
                'placeholder' => '-- Choisir un statut --',
                'constraints' => [
                    new NotBlank(['message' => 'Le statut est obligatoire'])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}