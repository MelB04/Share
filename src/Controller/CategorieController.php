<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CategorieType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CategorieRepository;

class CategorieController extends AbstractController
{

    #[Route('/private-addCategorie', name: 'app_categorie')]
    public function categorie(Request $request, EntityManagerInterface $em): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
       
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                $categorie -> setLibelle(ucfirst(strtolower($categorie->getLibelle())));
                
                $em->persist($categorie);
                $em->flush();
                $this->addFlash('notice','Catégorie créée');
                return $this->redirectToRoute('app_categorie');
            }
        }

        return $this->render('categorie/categorie.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/private-liste-categories', name: 'app_liste_categories')]
    public function listeCategories(CategorieRepository $categoriesRepository): Response
    {
        $categories = $categoriesRepository->findAll();
        return $this->render('categorie/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/private-modif-categorie/{id}', name: 'app_modif_categorie')] #il sait maintenant qu'il faut un id
    public function modifCategorie(Categorie $categorie, Request $request, EntityManagerInterface $em): Response ##categorie est l'entité qu'il doit aller chercher en fonction de l'id. Il comprend tout :)
    {
        $form = $this->createForm(CategorieType::class, $categorie); #il recupere les informations en préremplissant le formulaire
       
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                $categorie -> setLibelle(ucfirst(strtolower($categorie->getLibelle())));
                
                $em->persist($categorie); #ajouter ou modif
                $em->flush();
                $this->addFlash('notice','Catégorie modifiée');
                return $this->redirectToRoute('app_liste_categories');
            }
        }

        return $this->render('categorie/modifCategorie.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/private-del-categorie/{id}', name: 'app_del_categorie')]
    public function delCategorie(Categorie $categorie, EntityManagerInterface $em): Response ##categorie est l'entité qu'il doit aller chercher en fonction de l'id. Il comprend tout :)
    {
        $em->remove($categorie); #permet de supprimer la catégorie, l'element complet ($categorie = toute l'entité)
        $em->flush();
        $this->addFlash('notice','Catégorie supprimée');
        return $this->redirectToRoute('app_liste_categories'); 
    }

}
