<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message', null, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Donnez votre avis'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez écrire votre commentaire'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
