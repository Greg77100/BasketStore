<?php

namespace App\Form;

use App\Entity\BillingAdress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillingAdressType extends AbstractType
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
            
            ]
        )
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
            
            
        ])
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BillingAdress::class,
        ]);
    }
}
