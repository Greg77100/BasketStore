<?php

namespace App\Form;

use App\Entity\ShippingAdressOrder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShippingAdressOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('fullName', null, [
            'label' => 'Nom+Prénom*',
            'required' => false,
            'label_attr' => [
                'class' => 'text-dark'
            ],
            'attr' => [
                'placeholder' => 'Saisir votre nom',
            ],
            'help' => 'Saisir votre nom',
            'help_attr' => [
                'class' => 'fst-italic text-dark'
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez saisir votre nom'
                ])
            ]
        ])
        ->add('streetAdress', null, [
            'label' => 'Adresse*',
            'required' => false,
            'label_attr' => [
                'class' => 'text-dark'
            ],
            'attr' => [
                'placeholder' => 'Saisir votre adresse',
            ],
            'help' => 'Saisir votre adresse',
            'help_attr' => [
                'class' => 'fst-italic text-dark'
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez saisir votre adresse'
                ])
            ]
        ])
        ->add('city', null, [
            'label' => 'Ville*',
            'required' => false,
            'label_attr' => [
                'class' => 'text-dark'
            ],
            'attr' => [
                'placeholder' => 'Saisir votre ville',
            ],
            'help' => 'Saisir votre ville',
            'help_attr' => [
                'class' => 'fst-italic text-dark'
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez saisir votre ville'
                ])
            ]
        ])
        ->add('postalCode', null, [
            'label' => 'Code postal*',
            'required' => false,
            'label_attr' => [
                'class' => 'text-dark'
            ],
            'attr' => [
                'placeholder' => 'Saisir votre code postal',
            ],
            'help' => 'Saisir votre code postal',
            'help_attr' => [
                'class' => 'fst-italic text-dark'
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez saisir votre code postal'
                ]),
                new Length([
                    'min' => 5,
                    'max' => 5,
                    'minMessage' => 'Veuillez saisir au moins 5 chiffres',
                    'maxMessage' => 'Veuillez saisir au maximum 5 chiffres'
                ])
            ]
        ])
        ->add('phone',null, [
            'label' => 'Téléphone*',
            'required' => false,
            'label_attr' => [
                'class' => 'text-dark'
            ],
            'attr' => [
                'placeholder' => 'Saisir votre numéro de téléphone',
            ],
            'help' => 'Saisir votre numéro de téléphone',
            'help_attr' => [
                'class' => 'fst-italic text-dark'
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez saisir votre numéro de téléphone'
                ]),
                new Length([
                    'min' => 10,
                    'max' => 10,
                    'minMessage' => 'Veuillez saisir au moins 10 numéros',
                    'maxMessage' => 'Veuillez saisir au maximum 10 numéros'
                ])
            ]
        ])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShippingAdressOrder::class,
        ]);
    }
}
