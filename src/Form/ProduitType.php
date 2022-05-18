<?php

namespace App\Form;

use App\Entity\Marque;
use App\Entity\Matiere;
use App\Entity\Produit;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                "label" => "Titre du produit*",
                "required" => false,
                "attr" => [
                    "placeholder" => "Saisir le titre",
                    "class" => "bg-light"
                ],
                "help" => "Saisir un titre !!!",
                "label_attr" => [
                    'id' => "blabla"
                    // tableau des attr sur la balise label
                ],
                "row_attr" => [
                    "id" => "bizarre"
                    // tableau concernant la div qui englobe le label et l'input
                ],
                // "constraints" => [
                //     new NotBlank([
                //         "message" => "SAISIR UN TITRE"
                //     ]),
                //     new Length([
                //         "min" => 6,
                //         "max" => 15,
                //         "minMessage" => "6 MIN",
                //         "maxMessage" => "15 MAX"
                //     ])
                // ]
            ])

            ->add('prix', MoneyType::class, [
                "currency" => "JPY",
                "required" => false,
                "label" => "Prix du produit*"
            ])
            ->add('description', TextareaType::class, [
                "required" => false,
                "attr" => [
                    "rows" => 8
                ]
            ])

            ->add("categorie", EntityType::class, [ // élément relationel
                "class" => Categorie::class,        // relation avec quelle class
                "choice_label" => "nom",            // propriété à afficher
                //"expanded" => true, //  type radio ou checkbox(ManyToMany)
                "placeholder" => "Saisir une catégorie", // première option sans valeur 
                "required" => false
            ])

            ->add("marque", EntityType::class, [
                "class" => Marque::class,
                "choice_label" => "nom",
                "placeholder" => "Saisir une marque", 
                "required" => false
            ])

            ->add("matiere", EntityType::class, [
                "class" => Matiere::class,
                "choice_label" => "nom",
                "multiple" => true, // obligatoire car relation ManyToMany
                "attr" => [
                    "class" => "select2"
                ]
                //"expanded" => true // checkbox
            ])

            // ->add("test", ChoiceType::class, [ // ChoiceType ==> balise select
            //     "mapped" => false,
            //     "choices" => [ // tableau des options, key => sur le navigateur, value => valeur récupérée
            //         "test1" => 1,
            //         "test2" => 2
            //     ]
            // ])

            // ->add("test", TextType::class,[
            //     "mapped" => false // il n'y a pas de propriété "test" dans l'entity Produit
            // ])

            // ->add('Ajouter', SubmitType::class, [
            //     "attr" => [
            //         "class" => "col-md-12 mt-3 bg-success text-white"
            //     ]
            // ])
            
        ;
        if($options['ajouter'])
        {
            $builder->add('image', FileType::class, [
                "required" => false,
                //"data_class" => null,
                "attr" => [
                    "onchange" => "loadFile(event)"
                ]
            ]);
        }
        elseif($options['modifier'])
        {
            $builder->add('imageUpdate', FileType::class, [
                "required" => false,
                "mapped" => false,
                "attr" => [
                    "onchange" => "loadFile(event)"
                ]
            ]);
        }


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
            'ajouter' => false,
            "modifier" => false
        ]);
    }
}
