<?php

namespace App\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserIaPreferencesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('provider', ChoiceType::class, [
                'label' => 'Provider IA préféré',
                'choices' => [
                    'OpenAI' => 'openai',
                    'Anthropic' => 'anthropic',
                ],
            ])
            ->add('model', TextType::class, [
                'label' => 'Modèle IA préféré',
            ])
            ->add('tone', ChoiceType::class, [
                'label' => 'Ton des réponses',
                'choices' => [
                    'Formel' => 'formal',
                    'Neutre' => 'neutral',
                    'Amical' => 'friendly',
                ],
            ])
            ->add('detail', ChoiceType::class, [
                'label' => 'Niveau de détail',
                'choices' => [
                    'Court' => 'short',
                    'Normal' => 'normal',
                    'Détaillé' => 'detailed',
                ],
            ])
            ->add('language', TextType::class, [
                'label' => 'Langue préférée',
            ]);
    }
}
