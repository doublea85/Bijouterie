<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Commentaire;
use App\Filter\ProduitFilter;
use App\Form\CommentaireType;
use App\Form\FilterType;
use App\Repository\CommentaireRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{

    /*

        route : /fiche_produit
        nom de la route : "fiche_produit"

        vue dans le dossier produit/fiche_produit.html.twig

    */

    /**
     * @Route("/catalogue", name="catalogue")
     */
    public function catalogue(ProduitRepository $repoProduit, Request $request) : Response
    {
        //$prices = [400,1000];


        $filter = new ProduitFilter;
        //dd($filter);
        $form = $this->createForm(FilterType::class, $filter);


        $form->handleRequest($request);

        dump($filter);

        $produits = $repoProduit->findFiltre($filter);
        /*
            Repository = requête de selection de l'entity(table)

            findAll()
            find(int) 
            findBy(array)

            ->findTout()
            ->findId(1)
            ->findOrderPrix()
            ->findOrder("ASC")
            ->findLimit(4)
            ->findCategorie(5)
            ->findCategories([2])
            ->findMarques([2,3])
            ->findMatieres([4])
            ->findSomething("bracelet")
        */

        //dd($produits);

        


        return $this->render("produit/catalogue.html.twig", [
            "produits" => $produits,
            "formFilter" => $form->createView()
        ]);
    }

    /**
     * custom les paramètres des routes :
     * en procédural : fichier.php?nomParam=valueParam
     * SF => entre accolade le NOM du paramètre (nommé dans le twig)
     *      ==> affichage uniquement de la VALEUR du paramètre
     * 
     * @Route("/fiche_produit/{id}", name="fiche_produit")
     */
    public function fiche_produit(Produit $produit, Request $request, EntityManagerInterface $manager, CommentaireRepository $repoCommentaire)
    {
        /*
            le paramètre du route est envoyé en dépendance de la fonction 
            soit dans une variable du même nom que le paramètre 
            soit directement dans un objet 

            exemple :
            le paramètre id va est placé dans l'objet $produit sur la propriété qui porte le même nom
            

        */
                                //$id, ProduitRepository $repoProduit
        //dump($id);

        /*
            ->findAll() ==> SELECT * FROM produit
            ->find(int)    ==> SELECT * FROM produit WHERE id = $id
        */
        //$produit = $repoProduit->find($id);
        //dd($produit);

        //$commentaires = $repoCommentaire->findBy(["produit" => $produit]);

        $commentaires = $repoCommentaire->findBy([
            // key => value
            // key est le nom de la propriété de l'entity
            // value est la valeur recherchée
            "produit" => $produit

        ]);
        // SELECT * FROM commentaire WHERE produit = 3
        /*
            ->findAll() => SELECT * FROM table
            ->find(int) => SELECT * FROM table WHERE id = int
            ->findBy(array)
            [ key => value, key2 => value2]
            ==> SELECT * FROM table WHERE key = value AND key2 = value2

            la méthode se base sur une propriété créée par SF
            la propriété id 

            par contre la méthode findBy se base sur NOS propriétés (nom de propriété choisi)

        */

        //dd($commentaires);

        $commentaire = new Commentaire;
        $form = $this->createForm(CommentaireType::class, $commentaire);

        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid())
        {
            //dd($commentaire);
            $userCo = $this->getUser(); // méthode qui récupère l'objet issu de l'entity User de l'utilisateur connecté
            $commentaire->setDateAt(new \DateTimeImmutable('now'));
            $commentaire->setProduit($produit);// on est sur la route fiche_produit qui a un paramètre (id produit)
            $commentaire->setUser($userCo);
            

            //dd($commentaire);

            $manager->persist($commentaire);
            $manager->flush();





            return $this->redirectToRoute('fiche_produit', ['id' => $produit->getId()]);
        }


        return $this->render("produit/fiche_produit.html.twig", [
            "produit" => $produit,
            "formCommentaire" => $form->createView(),
            "commentaires" => $commentaires
        ]);
    }






}
