<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\MarqueRepository;
use App\Repository\MatiereRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin")
     */
    public function index(ProduitRepository $repoProduit, CategorieRepository $repoCategorie, MarqueRepository $repoMarque, MatiereRepository $repoMatiere ): Response
    {


        return $this->render('admin/index.html.twig', [
            "gestions" => $gestions
        ]);
    }
}
