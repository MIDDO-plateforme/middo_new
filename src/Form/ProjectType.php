<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du projet',
                'attr' => [
                    'placeholder' => 'Ex: Site web e-commerce',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom du projet est obligatoire'])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Décrivez votre projet...',
                    'class' => 'form-control',
                    'rows' => 5
                ]
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Brouillon' => 'draft',
                    'En cours' => 'in_progress',
                    'Terminé' => 'completed',
                    'Archivé' => 'archived'
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('budget', MoneyType::class, [
                'label' => 'Budget',
                'required' => false,
                'currency' => 'EUR',
                'attr' => [
                    'placeholder' => '0.00',
                    'class' => 'form-control'
                ]
            ])
            ->add('startDate', DateType::class, [
                'label' => 'Date de début',
                'required' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Date de fin',
                'required' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('members', EntityType::class, [
                'label' => 'Membres de l\'équipe',
                'class' => User::class,
                'choice_label' => 'email',
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'class' => 'form-control select2-multiple'
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