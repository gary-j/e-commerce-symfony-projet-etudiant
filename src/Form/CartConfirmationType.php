<?php

namespace App\Form;

use App\Entity\Purchase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartConfirmationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullname', TextType::class, [
                'label' => 'Votre nom complet pour la livraison',
                'attr' => [
                    'placeholder' => 'Nom Prénom'
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Votre adresse de livraison',
                'attr' => [
                    'placeholder' => 'adresse de livraison'
                ]
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code Postale',
                'attr' => [
                    'placeholder' => 'code postal'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Ville'
                ]
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
                'attr' => [
                    'placeholder' => 'Pays'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => Purchase::class
        ]);
    }
}
