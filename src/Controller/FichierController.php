<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Form\AjoutFichierType;
use App\Entity\Fichier;
use Doctrine\ORM\EntityManagerInterface;

class FichierController extends AbstractController
{
    #[Route('/profil-telechargement-fichier/{id}', name: 'telechargement-fichier', requirements: ["id"=>"\d+"] )]
    public function telechargementFichier(int $id, EntityManagerInterface $entityManagerInterface) {
     
        $repoFichier = $entityManagerInterface->getRepository(Fichier::class); 
        $fichier = $repoFichier->find($id);
        if ($fichier == null){
            $this->redirectToRoute('app_profil-ajout-fichier'); }
        else{
            return $this->file($this->getParameter('file_directory').'/'.$fichier->getNomServeur(), $fichier->getNomOriginal());
        } 
    } 


    
    #[Route('/profil-ajout-fichier', name: 'app_profil-ajout-fichier')]
    public function index(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManagerInterface): Response
    {
        $form = $this->createForm(AjoutFichierType::class);
        
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()){
                $fichier = $form->get('fichier')->getData();
                
                if($fichier){
                 $nomFichier = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                 $nomFichier = $slugger->slug($nomFichier);
                 $nomFichier = $nomFichier.'-'.uniqid().'.'.$fichier->guessExtension();
                    try{                 
                        $f = new Fichier();
                        $f->setNomServeur($nomFichier);
                        $f->setNomOriginal($fichier->getClientOriginalName());
                        $f->setDateEnvoi(new \Datetime());
                        $f->setExtension($fichier->guessExtension());
                        $f->setTaille($fichier->getSize());
                        $f->setProprietaire($this->getUser());
                       
                        $fichier->move($this->getParameter('file_directory'), $nomFichier);
                        $this->addFlash('notice', 'Fichier envoyé');  
                    }
                    catch(FileException $e){
                        $this->addFlash('notice', 'Erreur d\'envoi');
                    }      
                    
                    $entityManagerInterface->persist($f);
                    $entityManagerInterface->flush();   
                }
                return $this->redirectToRoute('app_profil-ajout-fichier');
            }
        } 

        return $this->render('fichier/ajout-fichier.html.twig', [
            'form'=> $form->createView()

        ]);
    }
}
