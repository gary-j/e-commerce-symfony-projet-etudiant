<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du Produit',
                'attr' => ['placeholder' => 'Produit...']
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Courte description',
                'attr' => ['placeholder' => 'Décrivez brièvement le produit pour le client']
            ])
            ->add('price', MoneyType::class, [
                'currency' => 'GBP',
                'label' => 'Prix de vente',
                'attr' => ['placeholder' => 'Prix en GBP']
            ])
            ->add('mainPicture', TextType::class, [
                'label' => "Image principal",
                'attr' => ['placeholder' => "Entrez l'url de l'image"]
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie du produit',
                'placeholder' => '-- Choisir une catégorie --',
                'class' => Category::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'choice_label' => function (Category $category) {
                    return strtoupper($category->getName());
                } //ou 'name' qui es le nom de la propriété
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
