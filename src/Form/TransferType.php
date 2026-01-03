<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class TransferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('recipient_email', EmailType::class, [
                'label' => 'Email du destinataire',
                'attr' => [
                    'placeholder' => 'exemple@middo.com',
                    'class' => 'form-control',
                    'autocomplete' => 'off',
                    'id' => 'recipient_email',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez saisir l\'email du destinataire.',
                    ]),
                    new Assert\Email([
                        'message' => 'Adresse email invalide.',
                    ]),
                ],
            ])
            ->add('amount', MoneyType::class, [
                'label' => 'Montant (€)',
                'currency' => 'EUR',
                'attr' => [
                    'placeholder' => '0.00',
                    'class' => 'form-control',
                    'min' => '0.01',
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
                        'value' => 0.01,
                        'message' => 'Le montant minimum est de 0.01 €.',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description (optionnel)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: Remboursement restaurant',
                    'class' => 'form-control',
                    'rows' => 3,
                    'maxlength' => 255,
                ],
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer le transfert',
                'attr' => [
                    'class' => 'btn btn-primary btn-lg w-100',
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
