<?php

namespace App\Controller; // App = src

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{

    //#[Route('/main', name: 'app_main')]

    /**
     * cette route permet d.... : commentaire 
     * localhost:8000 (local)
     * www.nomDeDomaine.fr
     * 
     * le premier argument est la ROUTE (URL)
     * le 2e name="" c'est le NOM de route (lien, redirection)
     * 
     * Les annotations sont dans des commentaires et sont pris en compte via @
     * 
     * pas de simples quotes, uniquement des DOUBLES 
     * 
     * @Route("/main", name="app_main")
     */
    public function index(): Response
    {
        $prenom = "Louis";

        // une fonction retourne toujours "qqch" 
        // c'est fonction est appelé par sa route




        // la méthode render() provenant de la class AbstractController permet de rattacher une vue à sa route 

        // les vues se situent toujours dans le dossier templates
        // écrire l'arborescence depuis template

        // La méthode render() a :
        // 1e argument OBLIGATOIRE : le nom du fichier (avec son arborescence)
        // 2e argument FACULTATIF : TABLEAU  des données à véhiculer du controller à sa vue
        return $this->render('main/index.html.twig', [
            'controller_name' => $prenom,
            //    key         =>   value
            //  variable en twig => valeur provenant du controller
        ]);
    }


    /**
     * Route principale
     * 
     * @Route("/", name="home")
     */
    public function home()
    {
        $tableau = [];

        //dump($tableau); 
        // dump permet d'afficher un élement (tableau variable objet) dans Symfony Profiler (la barre des tâches de Symfony située en bas de l'écran)

        $phrase = "Bienvenue à la bijouterieSF";

        $tableau = [10, "hello", 20];

        //dump($tableau);die; 
        //dd($tableau);
        // avec die; la suite du code n'est pas intréprété sur le navigateur



        return $this->render("main/home.html.twig", [] );
    }










} // fermeture de class



