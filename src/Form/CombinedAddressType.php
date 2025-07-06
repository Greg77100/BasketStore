<?php

namespace App\Form;

use App\Form\Model\AdressData;
use App\Form\BillingAdressOrderType;
use App\Form\ShippingAdressOrderType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CombinedAdressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
     // Ajouter l'adresse de livraison
    $builder->add('shipping', ShippingAdressOrderType::class, [
        'label' => 'Adresse de livraison'
    ]);

    // Ajouter l'adresse de facturation (seulement si 'sameAddress' est décoché)
    if (!$options['data']->getSameAdress()) {
        $builder->add('billing', BillingAdressOrderType::class, [
            'label' => 'Adresse de facturation'
        ]);
    }

    // Ajouter l'option pour utiliser la même adresse
    $builder->add('sameAdress', CheckboxType::class, [
        'label' => 'Utiliser la même adresse pour la facturation',
        'required' => false,
        'mapped' => true,
    ]);   
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdressData::class,
        ]);
    }

}
