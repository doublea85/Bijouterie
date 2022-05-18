<?php

namespace App\Repository;

use App\Entity\Produit;
use App\Filter\ProduitFilter;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Produit $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Produit $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Produit[] Returns an array of Produit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Produit
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    
    //Créaton de la fonction findAll()
    public function findTout()
    {
        return $this->createQueryBuilder("p")
            ->getQuery()
            ->getResult() // tableau d'objets (vide, 1, 2 ....)
        ;
    }


    //Création de la fonction find()
    public function findId($argument)
    {
        return $this->createQueryBuilder('p')
        ->andWhere("p.id = :idMarqueur")
        ->setParameter("idMarqueur", $argument)
        ->getQuery()
        ->getOneOrNullResult() // null ou directement l'objet
        ;

    }

    public function findOrderPrix()
    {
        return $this->createQueryBuilder("p")
            ->orderBy("p.prix", "DESC")
            ->getQuery()
            ->getResult()
        ;
    }

    // findOrder
    // depuis le controller, pouvoir choisir l'ordonnance (ASC, DESC)

    public function findOrder($order)
    {
        return $this->createQueryBuilder("p")
            ->orderBy("p.prix", $order)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findLimit($nb)
    {
        return $this->createQueryBuilder('p')
            ->setMaxResults($nb)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findCategorie($categorie)
    {
        return $this->createQueryBuilder("p")
            ->leftJoin("p.categorie", "c")
            ->andWhere("c.id = :cat")
            ->setParameter("cat", $categorie)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findCategories($categories)
    {
        return $this->createQueryBuilder("p")
            ->leftJoin("p.categorie", "c")
            ->andWhere("c.id IN(:cat)")
            ->setParameter("cat", $categories)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findMarques($marques)
    {
        return $this->createQueryBuilder("p")
            ->leftJoin("p.marque" , "m")
            ->andWhere("m.id IN(:marque)")
            ->setParameter("marque", $marques)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findMatieres($matieres)
    {
        return $this->createQueryBuilder("p")
            ->leftJoin("p.matiere" , "m")
            ->andWhere("m.id IN(:matiere)")
            ->setParameter("matiere", $matieres)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findSomething($recherche)
    {
        return $this->createQueryBuilder('p')
        ->leftJoin("p.categorie" , "c")
        ->andWhere("p.titre LIKE :recherche")
        ->orWhere("p.description LIKE :recherche")
        ->orWhere("p.prix LIKE :recherche")
        ->orWhere("c.nom LIKE :recherche")
        ->setParameter("recherche", "%$recherche%")
        ->getQuery()
        ->getResult()
        ;
    }



    /*
        e%  mot commence par un E
        %e  mot termine par un E
        %e% mot est composé de la lettre E (devant, milieu, fin)
    */


    // fonction findBetween()
    // rechercher des produits entre 2 sommes (du controller)
    public function findBetween($prices)
    {
        return $this->createQueryBuilder('p')
        ->andWhere('p.prix BETWEEN :min AND :max')
        ->setParameter('min', $prices[0])
        ->setParameter('max', $prices[1])
        ->getQuery()
        ->getResult()
        ;
    }

    public function findPrix($min, $max)
    {
        return $this->createQueryBuilder('p')
        ->andWhere('p.prix >= :min')
        ->andWhere('p.prix <= :max')
        ->setParameter('min', $min)
        ->setParameter('max', $max)
        ->getQuery()
        ->getResult()
        ;
    }





    // FILTRES
    public function findFiltre(ProduitFilter $filter)
    {
        $query = $this->createQueryBuilder("p")
        ->leftJoin("p.categorie" , "c")
        ->leftJoin("p.marque" , "mq")
        ->leftJoin("p.matiere" , "mt")
        ;
               
        if($filter->recherche)
        {
            $query = $query
            ->andWhere("p.titre LIKE :recherche")
            ->orWhere("p.description LIKE :recherche")
            ->orWhere("p.prix LIKE :recherche")
            ->orWhere("c.nom LIKE :recherche")
            ->orWhere("mt.nom LIKE :recherche")
            ->orWhere("mq.nom LIKE :recherche")
            ->setParameter("recherche", "%$filter->recherche%");
        }


        if($filter->categories)
        {
            $query = $query
            ->andWhere("c.id IN(:cat)")
            ->setParameter("cat", $filter->categories)
            ;
        }

        if($filter->marques)
        {
            $query = $query
            ->andWhere("mq.id IN(:mq)")
            ->setParameter("mq", $filter->marques)
            ;
        }

        if($filter->matieres)
        {
            $query = $query
            ->andWhere("mt.id IN(:mt)")
            ->setParameter("mt", $filter->matieres)
            ;
        }


        if($filter->min)
        {
            $query = $query
            ->andWhere("p.prix >= :min")
            ->setParameter("min", $filter->min)
            ;
        }

        if($filter->max)
        {
            $query = $query
            ->andWhere("p.prix <= :max")
            ->setParameter("max", $filter->max)
            ;
        }


        if($filter->order)
        {
            switch($filter->order)
            {
                case 1:
                    $query = $query
                        ->orderBy("p.prix", "ASC")
                    ;
                break;

                case 2:
                    $query = $query
                        ->orderBy("p.prix", "DESC")
                    ;
                break;

                case 3:
                    $query = $query
                        ->orderBy("p.titre", "ASC")
                    ;
                break;

                case 4:
                    $query = $query
                        ->orderBy("p.titre", "DESC")
                    ;
                break;

            }
        }









        return $query->getQuery()->getResult();
    }















}
