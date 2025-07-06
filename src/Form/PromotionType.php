<?php

namespace App\Form;



use App\Entity\Promotion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class PromotionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null,[
                'required' => false,
                'label' => 'Nom de la promotion*',
                'attr' => [
                    'placeholder' => 'Saisir le nom de la promotion'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le nom de la promotion'
                    ])
                ]
            ])

            ->add('description', null, [
                'label' => 'Description (facultatif)',
                'required' => false,
                'label_attr' => [
                    'class' => 'text-dark'
                ],
                'attr' => [
                    'placeholder' => 'Saisir la description de la promotion',
                    'rows' => 4
                ],
                'help' => 'Saisir la description du produit (200 caractères max)',
                'help_attr' => [
                    'class' => 'fst-italic text-dark'
                ],
                
                'constraints' => [
                    new Length([
                        'max' => 200,
                        'maxMessage' => 'Veuillez saisir moins de 200 caractères'
                    ])
                ]
            ])
            ->add('discountPercentage', NumberType::class, [
                'label' => 'Réduction (%)',
                'attr' => [
                    'placeholder' => 'Ex: 20',
                    'min' => 0,
                    'max' => 100
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un pourcentage de réduction']),
                    new Type([
                        'type' => 'numeric',
                        'message' => 'Veuillez entrer un nombre valide'
                    ]),
                    new Range([
                        'min' => 0,
                        'max' => 100,
                        'notInRangeMessage' => 'Le pourcentage doit être compris entre 0 et 100'
                    ])
                ]
            ])
            ->add('startDate', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'html5' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir une date de début']),
                    
                ]
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir une date de fin']),
                    
                    new GreaterThan([
                        'propertyPath' => 'parent.all[startDate].data',
                        'message' => 'La date de fin doit être postérieure à la date de début'
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Promotion::class,
        ]);
    }
}
