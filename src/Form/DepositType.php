<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class DepositType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', MoneyType::class, [
                'label' => 'Montant du dépôt (€)',
                'currency' => 'EUR',
                'attr' => [
                    'placeholder' => '0.00',
                    'class' => 'form-control form-control-lg',
                    'min' => '5.00',
                    'step' => '0.01',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez saisir un montant.',
                    ]),
                    new Assert\Positive([
                        'message' => 'Le montant doit être positif.',
                    ]),
                    new Assert\GreaterThanOrEqual([
                        'value' => 5.00,
                        'message' => 'Le montant minimum de dépôt est de 5.00 €.',
                    ]),
                    new Assert\LessThanOrEqual([
                        'value' => 5000.00,
                        'message' => 'Le montant maximum de dépôt est de 5000.00 €.',
                    ]),
                ],
            ])
            ->add('quick_amount', ChoiceType::class, [
                'label' => 'Montants rapides',
                'mapped' => false,
                'required' => false,
                'choices' => [
                    '10 €' => 10,
                    '25 €' => 25,
                    '50 €' => 50,
                    '100 €' => 100,
                    '250 €' => 250,
                    '500 €' => 500,
                ],
                'expanded' => true,
                'multiple' => false,
                'attr' => [
                    'class' => 'quick-amount-selector',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Continuer vers le paiement',
                'attr' => [
                    'class' => 'btn btn-success btn-lg w-100',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
