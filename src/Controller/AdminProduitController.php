<?php

namespace App\Controller;


use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * L'annotation placée avant la class, est héritée dans toutes les routes du Controller
 * @Route("/admin/produit")
 */
class AdminProduitController extends AbstractController
{
  
    /*
        AdminProduitController contient le CRUD (Create-Read-Update-Delete)

                ROUTE                                NOM DE LA ROUTE
        /admin/produit/afficher                      produit_afficher
        /admin/produit/ajouter                       produit_ajouter
        /admin/produit/modifier/{id}                 produit_modifier  <----------------------
        /admin/produit/supprimer/{id}                produit_supprimer
    */


    /*
        créer la route afficher
            - annotation
            - function
            - return la vue
            - créer un fichier html.twig  (produit_afficher.html.twig)
            - (situé dans admin_produit)

            - sur le twig : 
                - héritage 
                - blocks (title, h1, body)
                - h1 et title : mettre un titre : Gestion des produits

            - nav link pour cette route
    */

    /**
     * Tous les produits sous forme de tableau 
     * routes ajouter modifier supprimer 
     * 
     * @Route("/afficher", name="produit_afficher")
     */
    public function produit_afficher(ProduitRepository $repoProduit) : Response
    {

        /*

            Lorsqu'on créé une entity, est généré en même temps son Repository

            Le Repository d'une Entity permet d'effectuer les requêtes de SELECTion

            exemple :
            ProduitRepository correspond en SQL à
            SELECT ........ FROM produit .......... 

            de base, il existe quelques méthodes dans le repo

            méthode findAll() ==> équivalent à SELECT * FROM produit
            ------------------------------------------------------

            Pour utiliser le Repository qui est une class, on utilise un objet ou une instance issu de cette class

            Pour générer l'objet on le définit en DEPENDANCE de la fonction

            syntaxe :  Class $objet

            Attention, ne pas oublier qu'une class doit être importée 

        */

        $produits = $repoProduit->findAll();
        /*
            la méthode findAll() retourne un tableau d'objets
            ou alors RIEN
        */

        //dd($produits);





        return $this->render("admin_produit/produit_afficher.html.twig",[
            "produits" => $produits
        ]);
    }

    /**
     * @Route("/ajouter", name="produit_ajouter")
     */
    public function produit_ajouter(Request $request, EntityManagerInterface $manager)
    {

        $produit = new Produit;
        dump($produit);
        /*
            Entity
                -> table 
                -> repo
                -> form
                -> objet
        */

        $form = $this->createForm(ProduitType::class, $produit, ["ajouter" => true]);
        /* 
            La méthode createForm() permet de créer un objet sur le formulaire
            2 arguments obligatoires :
                - class où se situe le builder (dans le dossier Form)
                - l'objet

            3 argument facultatif : tablau des options
        */

        // récupération du traitement du formulaire
        // request contient les superglobales 
        // propriétés :
        // request ($_POST)
        // query ($_GET)
        $form->handleRequest($request);

        /*
            Si le formulaire est sousmis (type="submit")
            et s'il est valide (respect des conditions/ contraintes)
        */
        if($form->isSubmitted() AND $form->isValid())
        {
            dump($produit);
            $produit->setCreatedAt(new \DateTimeImmutable('now'));
            dump($produit);



            $imageFile = $form->get('image')->getData();
            /*
                la méthode get() de l'objet form permet d'acceder à un élement du formulaire
                l'argument est le nom de l'élement qu'on retrouve dans les méthodes add() du builder 
            */

            //dd($imageFile);
            /*
                $imageFile peut être soit null soit un objet de la class UploadedFile

                si  null ===> pas d'upload
                si objet ===> upload d'un fichier
            */
            if($imageFile) // traitement de l'image
            {
                // renommer le nom de l'image
                $nomImage = date('YmdHis') . "-" . uniqid() . "." . $imageFile->getClientOriginalExtension();
          
                //dd($nomImage);


                // déplacer le fichier dans le dossier public 
                $imageFile->move(
                    $this->getParameter('imageUpload'),
                    $nomImage
                );
                /*
                    la méthode move() permet de déclacer LE fichier dans le projet
                    2 arguments :
                        1- emplacement (le dossier avec son arborescence)
                        2- sous quel nom sera enregistré le fichier 

                    argument : $this->getParameter('imageUpload')
                    la méthode getParameter() provenant de la class AbstractController permet de rechercher un nom de paramètre (qui se situe comme argument de la méthode) dans le fichier :
                    config/services.yaml 

                    exemple : 
                    imageUpload: '%kernel.project_dir%/public/images/upload'

                    le nom de paramètre a pour valeur une arborescence dans le projet depuis le nom du projet
                    %kernel.project_dir% ==> signifie "bijouterie" dans ce projet

                */

                // attribuer le nom du fichier dans l'objet produit
                $produit->setImage($nomImage);

            }












            $manager->persist($produit);
            /*
                MVC => Model 
                    ==> Entity (table) 
                    ==> Repository (requête SELECT)
                    ==> EntityManagerInterface (requêtes INSERT INTO, UPDATE et DELETE)


                La méthode persist() permet d'insérer ou de modifier
                à l'écoute de l'id de l'objet (si null => insert si int => update)

                La méthode remove() permet la suppression 

                -----------------------------
                On persiste un objet, 
                cet objet est issu d'une class,
                cette class est une entity
                l'entity en bdd est une table


            */
            $manager->flush();// execution
            // après flush, (après envoie en bdd) on a toujours accès à l'objet qui a maintenant un ID
            //dd($produit); 
            // notification
            $this->addFlash("success", "Le produit N°" . $produit->getId() . " a bien été ajouté");

            // redirection
            return $this->redirectToRoute("produit_afficher");
            // 1e argument obligatoire : LE NOM DE LA ROUTE
            // 2e argument facultatif, le tableau des paramètres 

            // en twig son équivalent : href="{{ path('produit_afficher') }}"


            // PLUS RIEN APRES LE RETURN
        }





        return $this->render("admin_produit/produit_ajouter.html.twig",[
            "formProduit" => $form->createView()
        ]);
    }


    /**
     * @Route("/modifier/{id}", name="produit_modifier")
     */
    public function produit_modifier(Produit $produit, Request $request, EntityManagerInterface $manager)
    {
        //dd($produit);

        $form = $this->createForm(ProduitType::class, $produit, ['modifier' => true]);

        $form->handleRequest($request);

        if($form->isSubmitted() AND $form->isValid())
        {
            
            $imageFile = $form->get('imageUpdate')->getData();
            //dd($imageFile);
            /*
                produit avec image
                produit sans image

                SANS IMAGE ==> SANS IMAGE  ok
                SANS IMAGE ==> AVEC IMAGE  ok

                AVEC IMAGE ==> INCHANGÉ    ok
                AVEC IMAGE ==> NOUVELLE IMAGE  ok (supprimer l'ancienne image) ok
                AVEC IMAGE ==> SANS IMAGE


            */
            if($imageFile)
            {
                $nomImage = date("YmdHis") . "-" . uniqid() . "." . $imageFile->getClientOriginalExtension();

                $imageFile->move(
                    $this->getParameter('imageUpload'),
                    $nomImage
                );

                /*
                    Si le produit a déjà une image, il faut la supprimer

                    la fonction prédéfinie php unlink() permet de supprimer un fichier du projet
                    l'argument obligatoire c'est le fichier avec son arborescence dans le projet

                */
                if($produit->getImage())
                {
                    unlink($this->getParameter('imageUpload') . "/" . $produit->getImage() );

                    //'bijouterie/public/images/upload/2022....png'
                }

                $produit->setImage($nomImage);
            }
            $manager->persist($produit);
            $manager->flush();

            $this->addFlash('success', "Le produit N°" . $produit->getId() . " a bien été modifié");
            return $this->redirectToRoute("produit_afficher");
        }

        return $this->render("admin_produit/produit_modifier.html.twig", [
            "produit" => $produit,
            "formProduit" => $form->createView()
        ]);
    }



    /**
     * @Route("/image/supprimer/{id}", name="image_supprimer")
     */
    public function image_supprimer(Produit $produit, EntityManagerInterface $manager)
    {
        if($produit->getImage())
        {
            unlink($this->getParameter('imageUpload') . "/" . $produit->getImage() );

            $produit->setImage(NULL);
            $manager->persist($produit);
            $manager->flush();
    
            $this->addFlash("success", "L'image du produit N°" . $produit->getId() . " a bien été supprimée");
    
            return $this->redirectToRoute("produit_modifier", ["id" => $produit->getId()]);
            //<a href="{{ path('produit_modifier' , {"id" : produit.id } ) }}">
        }
        else
        {
            $this->addFlash('error', "Ce produit n'a pas d'image");
            return $this->redirectToRoute("produit_afficher");
        }

    }

    /**
     * @Route("/supprimer/{id}", name="produit_supprimer")
     */
    public function produit_supprimer(Produit $produit, EntityManagerInterface $manager)
    {
        if($produit->getImage())
        {
            unlink($this->getParameter('imageUpload') . "/" . $produit->getImage() );
        }
        
        $idProduit = $produit->getId();

        $manager->remove($produit);
        $manager->flush();
        //dd($produit);
        $this->addFlash('success', "Le produit N° $idProduit a bien été supprimé");

        return $this->redirectToRoute('produit_afficher');
        
    }






}
