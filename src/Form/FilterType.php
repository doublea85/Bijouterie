<?php

namespace App\Form;

use App\Entity\Marque;
use App\Entity\Matiere;
use App\Entity\Categorie;
use App\Filter\ProduitFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categories', EntityType::class, [
                "class" => Categorie::class,
                "choice_label" => "nom",
                "multiple" => true,
                "expanded" => true,
                "required" => false
            ])


            ->add('marques', EntityType::class, [
                "class" => Marque::class,
                "choice_label" => "nom",
                "multiple" => true,
                "expanded" => true,
                "required" => false
            ])

            ->add('matieres', EntityType::class, [
                "class" => Matiere::class,
                "choice_label" => "nom",
                "multiple" => true,
                "expanded" => true,
                "required" => false
            ])

            ->add("min", TextType::class, [
                "required" => false
            ])

            ->add("max", TextType::class, [
                "required" => false
            ])

            ->add("recherche", TextType::class, [
                "required" => false
            ])

            ->add("order", ChoiceType::class, [
                "required" => false,
                "choices" => [
                    "Prix Croissant" => 1,
                    "Prix Décroissant" => 2,
                    "Titre Croissant" => 3,
                    "Titre Décroissant" => 4
                ]
            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProduitFilter::class,
        ]);
    }
}
